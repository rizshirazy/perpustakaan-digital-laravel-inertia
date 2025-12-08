<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\FineResource;
use App\Models\Fine;
use Inertia\Response;

class FineFrontController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();

        $fines = Fine::select(['id', 'return_book_id', 'user_id', 'late_fee', 'other_fee', 'total_fee', 'fine_date', 'payment_status'])
            ->with(['returnBook.loan'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Front/Fines/Index', [
            'page_settings' => [
                'title'    => 'Denda',
                'subtitle' => 'Menampilkan semua denda peminjaman buku anda',
            ],
            'fines' => FineResource::collection($fines)->additional(
                [
                    'meta' => [
                        'has_pages' => $fines->hasPages(),
                    ]
                ]
            ),
        ]);
    }
}
