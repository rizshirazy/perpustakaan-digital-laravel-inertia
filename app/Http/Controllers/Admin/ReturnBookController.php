<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Enums\ReturnBookCondition;
use App\Enums\ReturnBookStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReturnBookRequest;
use App\Http\Resources\Admin\LoanResource;
use App\Http\Resources\Admin\ReturnBookResource;
use App\Models\Fine;
use App\Models\FineSetting;
use App\Models\Loan;
use App\Models\ReturnBook;
use App\Models\ReturnBookCheck;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

use function Symfony\Component\Clock\now;

class ReturnBookController extends Controller
{
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

            DB::transaction(function () use ($request, $loan) {

                $return_book = $loan->returnBook()->create([
                    'return_code' => $this->generateReturnBookCode(),
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

                $isOnTime = $return_book->isOnTime();
                $daysLate = $return_book->getDaysLate();

                $fineData = $this->calculateFines($return_book, $return_book_check, FineSetting::first(), $daysLate);

                if ($isOnTime) {
                    if ($fineData) {
                        flashMessage($fineData['message'], 'error');
                        return to_route('admin.return-books.index');
                    }
                } else {
                    if ($fineData) {
                        flashMessage($fineData['message'], 'error');
                        return to_route('admin.return-books.index');
                    }
                }
            });

            flashMessage('Transaksi Pengembalian Buku Berhasil');

            return to_route('admin.return-books.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.loans.index');
        }
    }

    private function generateReturnBookCode(): string
    {
        $returnDate = Carbon::now();

        $prefix = 'RN' . $returnDate->format('Ymd');

        $latestReturnBook = ReturnBook::query()
            ->whereDate('return_date', $returnDate->toDateString())
            ->latest('return_code')
            ->first();

        $latestSequence = $latestReturnBook
            ? (int) substr($latestReturnBook->return_code, -4)
            : 0;

        $sequence = str_pad($latestSequence + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}.{$sequence}";
    }

    private function createFine(ReturnBook $returnBook, float $lateFee, float $otherFee): Fine
    {
        return $returnBook->fine()->create([
            'user_id'   => $returnBook->user_id,
            'late_fee'  => $lateFee,
            'other_fee' => $otherFee,
            'total_fee' => $lateFee + $otherFee,
            'fine_date' => Carbon::today(),
        ]);
    }

    private function calculateFines(ReturnBook $returnBook, ReturnBookCheck $returnBookCheck, FineSetting $fineSetting, int $daysLate): ?array
    {
        $late_fee = $fineSetting->late_fee_per_day * $daysLate;

        switch ($returnBookCheck->condition->value) {
            case ReturnBookCondition::DAMAGED->value:
                $other_fee = ($fineSetting->damage_fee_percentage / 100) * $returnBook->book->price;

                $returnBook->update([
                    'status' => ReturnBookStatus::FINE->value
                ]);

                $this->createFine($returnBook, $late_fee, $other_fee);

                return [
                    'message' => 'Kondisi buku rusak, harus membayar denda kerusakan',
                ];

                break;
            case ReturnBookCondition::LOST->value:
                $other_fee = ($fineSetting->lost_fee_percentage / 100) * $returnBook->book->price;

                $returnBook->update([
                    'status' => ReturnBookStatus::FINE->value
                ]);

                $this->createFine($returnBook, $late_fee, $other_fee);

                return [
                    'message' => 'Kondisi buku hilang, harus membayar denda kehilangan buku',
                ];

                break;
            default:
                if ($daysLate > 0) {
                    $returnBook->update([
                        'status' => ReturnBookStatus::FINE->value
                    ]);

                    $this->createFine($returnBook, $late_fee, 0);

                    return [
                        'message' => 'Terlambat mengembalikan buku, harus membayar denda keterlambatan',
                    ];
                } else {
                    $returnBook->update([
                        'status' => ReturnBookStatus::RETURNED->value
                    ]);

                    return null;
                }
                break;
        }
    }
}
