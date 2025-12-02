<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockRequest;
use App\Http\Resources\Admin\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Response;

class BookStockReportController extends Controller
{
    public function index(): Response
    {
        $stocks = Stock::select(['stocks.*'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with('book')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/BookStockReports/Index', [
            'page_settings' => [
                'title'    => 'Laporan Stok Buku',
                'subtitle' => 'Menampilkan laporan stok buku yang tersedia di perpustakaan',
            ],
            'page_data'     => [
                'stocks' => StockResource::collection($stocks)->additional(
                    [
                        'meta' => [
                            'has_pages' => $stocks->hasPages(),
                        ]
                    ]
                ),
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    public function edit(Stock $stock): Response
    {
        return inertia('Admin/BookStockReports/Edit', [
            'page_settings' => [
                'title'    => 'Edit Stok Buku',
                'subtitle' => 'Halaman untuk mengedit data stok buku pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.book-stock-reports.update', $stock),
            ],
            'stock' => StockResource::make($stock->load('book')),
        ]);
    }

    public function update(StockRequest $request, Stock $stock): \Illuminate\Http\RedirectResponse
    {
        try {
            $minimum_total = $stock->available + $stock->loaned + $stock->lost + $stock->damaged;

            if ($request->total < $minimum_total) {
                flashMessage(MessageType::ERROR->message("Total stok tidak boleh kurang dari jumlah buku yang tersedia, dipinjam, hilang, atau rusak."), 'error');

                return to_route('admin.book-stock-reports.edit', $stock);
            }

            $stock->update([
                'total'     => $request->total,
                'available' => $request->available,
            ]);

            flashMessage("Stok buku berhasil diperbarui.");

            return to_route('admin.book-stock-reports.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.book-stock-reports.index');
        }
    }
}
