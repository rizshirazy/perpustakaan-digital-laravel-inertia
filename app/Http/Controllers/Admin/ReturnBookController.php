<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateReturnBookCode;
use App\Actions\ProcessReturnBookFine;
use App\Enums\MessageType;
use App\Enums\ReturnBookCondition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnBookRequest;
use App\Http\Resources\Admin\LoanResource;
use App\Http\Resources\Admin\ReturnBookResource;
use App\Models\FineSetting;
use App\Models\Loan;
use App\Models\ReturnBook;
use App\Models\ReturnBookCheck;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class ReturnBookController extends Controller
{
    public function __construct(
        private ProcessReturnBookFine $processReturnBookFine,
        private GenerateReturnBookCode $generateReturnBookCode,
    )
    {
    }

    public function index(): Response
    {
        $return_books = ReturnBook::select(['id', 'return_code', 'loan_id', 'user_id', 'book_id', 'return_date', 'status'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['loan', 'book', 'user', 'fine', 'returnBookCheck'])
            ->latest('created_at')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/ReturnBooks/Index', [
            'return_books' => ReturnBookResource::collection($return_books)->additional([
                'meta' => [
                    'has_pages' => $return_books->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Pengembalian',
                'subtitle' => 'Menampilkan semua pengembalian buku yang tercatat pada sistem'
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    public function create(Loan $loan): RedirectResponse|Response
    {
        if ($loan->returnBook()->exists()) {
            return to_route('admin.loans.index');
        }

        if (!FineSetting::first()) {
            return to_route('admin.fine-settings.edit');
        }

        $loan->load(['user', 'book' => fn($q) => $q->with('publisher')]);

        return inertia('Admin/ReturnBooks/Create', [
            'page_settings' => [
                'title'    => 'Pengembalian',
                'subtitle' => 'Halaman untuk mencatat transaksi pengembalian. Klik kembalikan setelah selesai.',
                'method'   => 'POST',
                'action'   => route('admin.return-books.store', $loan->loan_code),
            ],
            'page_data' => LoanResource::withOptions($loan, [
                'conditions'  => ReturnBookCondition::options(),
            ]),
        ]);
    }

    public function store(ReturnBookRequest $request, Loan $loan): RedirectResponse
    {
        try {
            $fineData = null;

            DB::transaction(function () use ($request, $loan, &$fineData) {

                $return_book = $loan->returnBook()->create([
                    'return_code' => ($this->generateReturnBookCode)(),
                    'book_id'     => $loan->book->id,
                    'user_id'     => $loan->user->id,
                    'return_date' => Carbon::today(),
                ]);

                $return_book_check = $return_book->returnBookCheck()->create([
                    'condition' => $request->condition,
                    'notes'     => $request->notes,
                ]);

                match ($return_book_check->condition->value) {
                    ReturnBookCondition::GOOD->value    => $loan->book->stock_return_loan(),
                    ReturnBookCondition::DAMAGED->value => $loan->book->stock_damaged(),
                    ReturnBookCondition::LOST->value    => $loan->book->stock_lost(),
                };

                $daysLate = $return_book->getDaysLate();
                $fineData = ($this->processReturnBookFine)($return_book, $return_book_check, FineSetting::first(), $daysLate);
            });

            flashMessage(
                $fineData['message'] ?? 'Transaksi Pengembalian Buku Berhasil',
                $fineData ? 'error' : 'success'
            );

            return to_route('admin.return-books.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.loans.index');
        }
    }

}
