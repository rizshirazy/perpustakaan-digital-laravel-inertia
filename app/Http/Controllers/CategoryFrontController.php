<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\BookResource;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Response;

class CategoryFrontController extends Controller
{
    public function index(): Response
    {
        $categories = Category::orderBy('name')->paginate(8);

        return inertia('Front/Categories/Index', [
            'page_settings' => [
                'title'    => 'Kategori',
                'subtitle' => 'Menampilkan semua kategori buku yang tersedia di perpustakaan',
            ],
            'categories' => CategoryResource::collection($categories)->additional([
                'meta' => [
                    'has_pages' => $categories->hasPages(),
                ]
            ]),
        ]);
    }

    public function show(Category $category): Response
    {
        $books = Book::with('category')
            ->where('category_id', $category->id)
            ->paginate(12);

        return inertia('Front/Categories/Show', [
            'page_settings' => [
                'title'    => $category->name,
                'subtitle' => 'Menampilkan semua buku pada kategori ' . $category->name,
            ],
            'books' => BookResource::collection($books)->additional([
                'meta' => [
                    'has_pages' => $books->hasPages(),
                ]
            ]),
        ]);
    }
}
