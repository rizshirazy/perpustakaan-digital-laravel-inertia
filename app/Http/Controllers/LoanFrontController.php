<?php

namespace App\Http\Controllers;

use App\Actions\GenerateLoanCode;
use App\Enums\MessageType;
use App\Http\Resources\Admin\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class LoanFrontController extends Controller
{
    public function __construct(private GenerateLoanCode $generateLoanCode) {}

    public function index(): Response
    {

        $loans = Loan::where('user_id', auth()->user()->id)
            ->with('book')
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Front/Loans/Index', [
            'page_settings' => [
                'title'    => 'Peminjaman',
                'subtitle' => 'Menampilkan data transaksi peminjaman anda',
            ],
            'loans' => LoanResource::collection($loans)->additional([
                'meta' => [
                    'has_pages' => $loans->hasPages()
                ]
            ]),
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    public function store(Book $book): RedirectResponse
    {
        try {
            $user = auth()->user();

            if (Loan::checkLoanBook($user->id, $book->id)) {
                flashMessage(MessageType::ERROR->message('- Anda sudah meminjam buku ini'), 'error');

                return to_route('front.books.show', $book->slug);
            }

            // Cek stok
            if ($book->stock->available <= 0) {
                flashMessage('- Stock buku tidak tersedia', 'error');
                return to_route('front.books.show', $book->slug);
            }

            DB::transaction(function () use ($user, $book) {

                // Create loan
                $loan = Loan::create([
                    'loan_code' => ($this->generateLoanCode)(),
                    'user_id'   => $user->id,
                    'book_id'   => $book->id,
                    'loan_date' => now()->toDateString(),
                    'due_date'  => now()->addDays(7)->toDateString(),
                ]);

                // Update stok
                $loan->book->stock_loan();
            });

            flashMessage(MessageType::CREATED->message('Transaksi Peminjaman'));

            return to_route('front.loans.index');
        } catch (\Throwable $e) {
            logger()->error($e);

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('front.loans.index');
        }
    }

    public function show(Loan $loan): Response
    {
        $loan->load('book');

        return inertia('Front/Loans/Show', [
            'page_settings' => [
                'title'    => 'Detail Peminjaman',
                'subtitle' => 'Menampilkan detail transaksi peminjaman buku anda',
            ],
            'loan' => LoanResource::make($loan)
        ]);
    }
}
