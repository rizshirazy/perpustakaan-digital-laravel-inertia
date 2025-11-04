<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Traits\HasFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Throwable;

class CategoryController extends Controller
{

    use HasFile;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'cover'])
            ->when(request()->search, function ($query, $value) {
                $query->whereAny(['name', 'slug'], 'ILIKE', "%{$value}%");
            })
            ->when(request()->field && request()->direction, fn($query) => $query->orderBy(request()->field, request()->direction))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/Categories/Index', [
            'categories' => CategoryResource::collection($categories)->additional([
                'meta' => [
                    'has_pages' => $categories->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Kategori',
                'subtitle' => 'Menampilkan semua kategori yang tersedia pada sistem.',
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
        return inertia('Admin/Categories/Create', [
            'page_settings' => [
                'title'    => 'Tambah Kategori',
                'subtitle' => 'Halaman untuk membuat kategori baru pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.categories.store'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            Category::create(
                [
                    'name'        => $name = $request->name,
                    'slug'        => str()->lower(str()->slug($name) . '-' . str()->random(3)),
                    'description' => $request->description,
                    'cover'       => $this->upload_file($request, 'cover', 'categories'),
                ]
            );

            flashMessage(MessageType::CREATED->message('Kategori'));

            return to_route('admin.categories.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.categories.index');
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
    public function edit(Category $category): Response
    {
        return inertia('Admin/Categories/Edit', [
            'page_settings' => [
                'title'    => 'Edit Kategori',
                'subtitle' => 'Halaman untuk memperbarui data kategori pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.categories.update', $category),
            ],
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $category->update(
                [
                    'name'        => $name = $request->name,
                    'slug'        => $name !== $category->name ? str()->lower(str()->slug($name) . '-' . str()->random(3)) : $category->slug,
                    'description' => $request->description,
                    'cover'       => $this->update_file($request, $category, 'cover', 'categories'),
                ]
            );

            flashMessage(MessageType::UPDATED->message('Kategori'));

            return to_route('admin.categories.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.categories.index');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $this->delete_file($category, 'cover');
            $category->delete();

            flashMessage(MessageType::DELETED->message('Kategori'));

            return to_route('admin.categories.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.categories.index');
        }
    }
}
