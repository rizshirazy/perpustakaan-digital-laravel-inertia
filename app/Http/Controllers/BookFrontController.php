<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\BookResource;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Response;

class BookFrontController extends Controller
{
    public function index(): Response
    {
        $categories = Category::whereHas('books')
            ->with(['books' => fn($q) => $q->limit(4)])
            ->orderBy('name')
            ->get();

        return inertia('Front/Books/Index', [
            'page_settings' => [
                'title'    => 'Buku',
                'subtitle' => 'Menampilkan semua buku yang tersedia di perpustakaan',
            ],
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Book $book): Response
    {
        $book->load(['publisher', 'category', 'stock']);

        return inertia('Front/Books/Show', [
            'page_settings' => [
                'title'    => $book->title,
                'subtitle' => 'ISBN ' . $book->isbn,
            ],
            'book' => BookResource::make($book),
        ]);
    }
}
