<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ReturnBookResource;
use App\Models\ReturnBook;
use Inertia\Response;

class FineController extends Controller
{
    public function show(ReturnBook $returnBook): Response
    {
        $returnBook->load([
            'loan',
            'book.publisher',
            'user',
            'fine',
            'returnBookCheck',
        ]);

        return inertia('Admin/Fines/Show', [
            'page_settings' => [
                'title'    => 'Denda',
                'subtitle' => 'Ringkasan denda dan detail pengembalian untuk transaksi ini.'
            ],
            'page_data' => ReturnBookResource::make($returnBook)
        ]);
    }
}
