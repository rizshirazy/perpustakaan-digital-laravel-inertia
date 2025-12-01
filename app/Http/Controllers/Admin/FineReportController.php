<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FinePaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\FineResource;
use App\Http\Resources\Admin\MostFineMemberResource;
use App\Models\Fine;
use Illuminate\Http\Request;
use Inertia\Response;

class FineReportController extends Controller
{
    public function index(): Response
    {
        $fines = Fine::select(['id', 'return_book_id', 'user_id', 'late_fee', 'other_fee', 'total_fee', 'fine_date', 'payment_status'])
            ->with(['returnBook.loan.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Admin/FineReports/Index', [
            'page_settings' => [
                'title'    => 'Laporan Denda',
                'subtitle' => 'Menampilkan laporan denda peminjaman buku',
            ],
            'page_data'     => [
                'fines' => FineResource::collection($fines)->additional(
                    [
                        'meta' => [
                            'has_pages' => $fines->hasPages(),
                        ]
                    ]
                ),
                'top_members_by_fines' => MostFineMemberResource::collection(
                    Fine::select(['user_id'])
                        ->selectRaw('SUM(total_fee) as total_fines')
                        ->groupBy('user_id')
                        ->with('user')
                        ->orderByDesc('total_fines')
                        ->take(5)
                        ->get()
                ),
                'fine_paid'   => Fine::where('payment_status', FinePaymentStatus::SUCCESS->value)->sum('total_fee'),
                'fine_unpaid' => Fine::whereNot('payment_status', FinePaymentStatus::SUCCESS->value)->sum('total_fee'),
                'total_fines' => Fine::totalFines(),
            ],
        ]);
    }
}
