<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateBookCode;
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

    public function __construct(private GenerateBookCode $generateBookCode)
    {
    }
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
            $category = Category::find($request->category_id);

            Book::create([
                'book_code'        => ($this->generateBookCode)($request->publication_year, $category),
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
            $category = Category::find($request->category_id);
            $book->update([
                'book_code'        => $book->publication_year != $request->publication_year || $book->category->id != $request->category_id
                    ? ($this->generateBookCode)($request->publication_year, $category)
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
