<?php

namespace App\Actions;

use App\Models\Book;
use App\Models\Category;

class GenerateBookCode
{
    public function __invoke(int $publicationYear, Category $category, ?int $manualOrder = null): string
    {
        $cat = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 5));
        $prefix = 'CA' . $publicationYear . '.' . $cat . '.';

        if ($manualOrder !== null) {
            return $prefix . str_pad($manualOrder, 4, '0', STR_PAD_LEFT);
        }

        $lastBook = Book::query()
            ->where('book_code', 'like', $prefix . '%')
            ->orderByRaw("CAST((regexp_matches(book_code, '\\d{4}$'))[1] AS INTEGER) DESC")
            ->first();

        $order = $lastBook ? (int) substr($lastBook->book_code, -4) + 1 : 1;

        return $prefix . str_pad($order, 4, '0', STR_PAD_LEFT);
    }
}
