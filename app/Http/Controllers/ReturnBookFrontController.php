<?php

namespace App\Http\Controllers;

use App\Actions\GenerateReturnBookCode;
use App\Enums\MessageType;
use App\Http\Resources\Admin\ReturnBookResource;
use App\Models\Book;
use App\Models\Loan;
use App\Models\ReturnBook;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class ReturnBookFrontController extends Controller
{
    public function __construct(
        private GenerateReturnBookCode $generator,
    ) {}

    public function index(): Response
    {
        $user = auth()->user();

        $return_books = ReturnBook::select(['id', 'return_code', 'loan_id', 'user_id', 'book_id', 'return_date', 'status'])
            ->where('user_id', $user->id)
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['loan', 'book', 'user', 'fine', 'returnBookCheck'])
            ->latest('created_at')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Front/ReturnBooks/Index', [
            'return_books' => ReturnBookResource::collection($return_books)->additional([
                'meta' => [
                    'has_pages' => $return_books->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Pengembalian',
                'subtitle' => 'Menampilkan data transaksi pengembalian buku anda'
            ],
            'page_data' => [
                'returned' => ReturnBook::member($user->id)->returned()->count(),
                'fine'     => ReturnBook::member($user->id)->fine()->count(),
                'checked'  => ReturnBook::member($user->id)->checked()->count(),
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    public function show(ReturnBook $returnBook): Response
    {
        $returnBook->load(['loan.book.publisher', 'fine', 'returnBookCheck']);

        return inertia('Front/ReturnBooks/Show', [
            'page_settings' => [
                'title'    => 'Detail Pengembalian',
                'subtitle' => 'Menampilkan detail transaksi pengembalian buku anda'
            ],
            'return_book' => ReturnBookResource::make($returnBook)
        ]);
    }

    public function store(Book $book, Loan $loan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($book, $loan, &$fineData) {

                $return_book = $loan->returnBook()->create([
                    'return_code' => ($this->generator)(),
                    'book_id'     => $book->id,
                    'user_id'     => auth()->user()->id,
                    'return_date' => Carbon::today(),
                ]);
            });

            flashMessage('Buku anda akan diperiksa oleh petugas kami.');

            return to_route('front.loans.show', [$loan->loan_code]);
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('front.loans.show', [$loan->loan_code]);
        }
    }
}
