<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoanRequest;
use App\Http\Resources\Admin\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $loans = Loan::query()->select(['id', 'loan_code', 'user_id', 'book_id', 'loan_date', 'due_date'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['book', 'user', 'returnBook'])
            ->latest('created_at')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/Loans/Index', [
            'loans' => LoanResource::collection($loans)->additional([
                'meta' => [
                    'has_pages' => $loans->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Peminjaman',
                'subtitle' => 'Menampilkan semua peminjaman yang tercatat pada sistem'
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
        return inertia('Admin/Loans/Create', [
            'page_settings' => [
                'title'    => 'Tambah Transaksi Peminjaman',
                'subtitle' => 'Halaman untuk mencatat transaksi peminjaman pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.loans.store')
            ],
            'page_data' => LoanResource::onlyOptions([
                'loan_date' => Carbon::now()->isoFormat('DD MMM YYYY'),
                'due_date'  => Carbon::now()->addDays(7)->isoFormat('DD MMM YYYY'),
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LoanRequest $request): RedirectResponse
    {
        try {

            if (Loan::checkLoanBook($request->user_id, $request->book_id)) {
                flashMessage(MessageType::ERROR->message('- Pengguna sudah meminjam buku ini'), 'error');

                return to_route('admin.loans.create');
            }

            $book = Book::with('stock')->findOrFail($request->book_id);

            // Cek stok
            if ($book->stock->available <= 0) {
                flashMessage('- Stock buku tidak tersedia', 'error');
                return to_route('admin.loans.create');
            }

            DB::transaction(function () use ($request, $book) {

                // Create loan
                $loan = Loan::create([
                    'loan_code' => $this->generateLoanCode(),
                    'user_id'   => $request->user_id,
                    'book_id'   => $book->id,
                    'loan_date' => now()->toDateString(),
                    'due_date'  => now()->addDays(7)->toDateString(),
                ]);

                // Update stok
                $loan->book->stock_loan();
            });

            flashMessage(MessageType::CREATED->message('Transaksi Peminjaman'));

            return to_route('admin.loans.index');
        } catch (\Throwable $e) {
            logger()->error($e);

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.loans.index');
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
    public function edit(Loan $loan)
    {
        $loan->load(['book', 'user']);

        return inertia('Admin/Loans/Edit', [
            'page_settings' => [
                'title'    => 'Perbarui Transaksi Peminjaman',
                'subtitle' => 'Halaman untuk memperbarui transaksi peminjaman pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.loans.update', $loan)
            ],
            'page_data' => LoanResource::withOptions($loan, [
                'loan_date' => $loan->loan_date->isoFormat('DD MMM YYYY'),
                'due_date'  => $loan->due_date->isoFormat('DD MMM YYYY'),
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LoanRequest $request, Loan $loan)
    {
        try {
            $loan->load('book.stock');

            $bookChanged = (int) $request->book_id !== (int) $loan->book_id;

            if ($bookChanged && Loan::checkLoanBook($request->user_id, $request->book_id)) {
                flashMessage(MessageType::ERROR->message('- Pengguna sudah meminjam buku ini'), 'error');

                return to_route('admin.loans.edit', $loan);
            }

            $newBook = null;

            if ($bookChanged) {
                $newBook = Book::with('stock')->findOrFail($request->book_id);

                if (! $newBook->stock || $newBook->stock->available <= 0) {
                    flashMessage(MessageType::ERROR->message('- Stock buku tidak tersedia'), 'error');

                    return to_route('admin.loans.edit', $loan);
                }
            }

            DB::transaction(function () use ($request, $loan, $bookChanged, $newBook) {
                if ($bookChanged && $loan->book) {
                    $loan->book->stock_return_loan();
                }

                $loan->update([
                    'user_id' => $request->user_id,
                    'book_id' => $request->book_id,
                ]);

                if ($bookChanged && $newBook) {
                    $newBook->stock_loan();
                }
            });

            flashMessage(MessageType::UPDATED->message('Transaksi Peminjaman'));

            return to_route('admin.loans.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.loans.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan): RedirectResponse
    {
        try {
            $loan->load('book.stock');

            if ($loan->book) {
                $loan->book->stock_return_loan();
            }

            $loan->delete();

            flashMessage(MessageType::DELETED->message('Transaksi Peminjaman'));

            return to_route('admin.loans.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.loans.index');
        }
    }

    private function generateLoanCode(): string
    {
        $loanDate = Carbon::now();

        $prefix = 'LN' . $loanDate->format('Ymd');

        $latestLoan = Loan::query()
            ->whereDate('loan_date', $loanDate->toDateString())
            ->latest('loan_code')
            ->first();

        $latestSequence = $latestLoan
            ? (int) substr($latestLoan->loan_code, -4)
            : 0;

        $sequence = str_pad($latestSequence + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}.{$sequence}";
    }
}
