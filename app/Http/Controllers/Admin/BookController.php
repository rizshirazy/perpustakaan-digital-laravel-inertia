<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookStatus;
use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Http\Resources\Admin\BookResource;
use App\Models\Book;
use App\Models\Category;
use App\Traits\HasFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class BookController extends Controller
{
    use HasFile;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $books = Book::query()
            ->select([
                'id',
                'book_code',
                'title',
                'slug',
                'author',
                'publication_year',
                'isbn',
                'language',
                'number_of_pages',
                'status',
                'price',
                'category_id',
                'publisher_id',
                'created_at'
            ])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['category', 'stock', 'publisher'])
            ->paginate(request()->load ?? 10)
            ->withQueryString();


        return inertia('Admin/Books/Index', [
            'books' => BookResource::collection($books)->additional([
                'meta' => [
                    'has_pages' => $books->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Buku',
                'subtitle' => 'Menampilkan semua buku yang terdaftar pada sistem.',
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Admin/Books/Create', [
            'page_settings' => [
                'title'    => 'Tambah Buku',
                'subtitle' => 'Halaman untuk menambahkan koleksi buku.',
                'method'   => 'POST',
                'action'   => route('admin.books.store'),
            ],
            'page_data' => BookResource::onlyOptions([
                'publicationYears' => array_reverse(
                    range(2000, now()->year)
                ),
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request): RedirectResponse
    {
        try {
            Book::create([
                'book_code'        => $this->bookCode($request->publication_year, $request->category_id),
                'title'            => $title = $request->title,
                'slug'             => str()->lower(str()->slug($title) . '-' . str()->random(3)),
                'author'           => $request->author,
                'publication_year' => $request->publication_year,
                'isbn'             => $request->isbn,
                'language'         => $request->language,
                'synopsis'         => $request->synopsis,
                'number_of_pages'  => $request->number_of_pages,
                'status'           => $request->total > 0 ? BookStatus::AVAILABLE->value : BookStatus::UNAVAILABLE->value,
                'cover'            => $this->upload_file($request, 'cover', 'books'),
                'price'            => $request->price,
                'category_id'      => $request->category_id,
                'publisher_id'     => $request->publisher_id,
            ]);

            flashMessage(MessageType::CREATED->message('Buku'));

            return to_route('admin.books.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.books.index');
        }
    }
    private function bookCode(int $publication_year, int $category_id): string
    {
        $category = Category::find($category_id);

        $cat = str(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 5))->upper();
        $book_code_prefix = 'CA' . $publication_year . '.' . $cat . '.';

        $last_book = Book::query()
            ->where('book_code', 'like', $book_code_prefix . '%')
            ->orderByRaw("CAST((regexp_matches(book_code, '\d{4}$'))[1] AS INTEGER) DESC")
            ->first();

        $order = 1;

        if ($last_book) {
            $last_order = (int) substr($last_book->book_code, -4);
            $order = $last_order + 1;
        }

        $ordering = str_pad($order, 4, '0', STR_PAD_LEFT);

        return $book_code_prefix . $ordering;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): Response
    {
        $book->load(['category', 'publisher', 'stock']);

        return inertia('Admin/Books/Edit', [
            'page_settings' => [
                'title'    => 'Edit Buku',
                'subtitle' => 'Halaman untuk memperbarui data buku pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.books.update', $book),
            ],
            'page_data' => BookResource::withOptions($book, [
                'publicationYears' => array_reverse(
                    range(2000, now()->year)
                ),
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        try {
            $book->update([
                'book_code'        => $book->publication_year != $request->publication_year || $book->category->id != $request->category_id
                    ? $this->bookCode($request->publication_year, $request->category_id)
                    : $book->book_code,
                'title'            => $title = $request->title,
                'slug'             => $title != $book->title ? str()->lower(str()->slug($title) . '-' . str()->random(3)) : $book->slug,
                'author'           => $request->author,
                'publication_year' => $request->publication_year,
                'isbn'             => $request->isbn,
                'language'         => $request->language,
                'synopsis'         => $request->synopsis,
                'number_of_pages'  => $request->number_of_pages,
                'status'           => $request->total > 0 ? BookStatus::AVAILABLE->value : BookStatus::UNAVAILABLE->value,
                'cover'            => $this->update_file($request, $book, 'cover', 'books'),
                'price'            => $request->price,
                'category_id'      => $request->category_id,
                'publisher_id'     => $request->publisher_id,
            ]);

            flashMessage(MessageType::UPDATED->message('Buku'));

            return to_route('admin.books.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.books.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): RedirectResponse
    {
        try {
            $this->delete_file($book, 'cover');
            $book->delete();

            flashMessage(MessageType::DELETED->message('Buku'));

            return to_route('admin.books.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.books.index');
        }
    }
}
