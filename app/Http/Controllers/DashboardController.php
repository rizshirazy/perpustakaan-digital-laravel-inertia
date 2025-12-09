<?php

namespace App\Http\Controllers;

use App\Enums\FinePaymentStatus;
use App\Http\Resources\Admin\TransactionLoanResource;
use App\Http\Resources\Admin\TransactionReturnBookResource;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\ReturnBook;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $loans = Loan::recentForUser($user, 5);
        $returnBooks = ReturnBook::recentForUser($user, 5);

        return inertia('Dashboard', [
            'page_settings' => [
                'title'    => 'Dashboard',
                'subtitle' => 'Selamat datang di perpustakaan cendikia',
            ],
            'page_data' => [
                'transaction_chart'    => $this->transactionChart(),
                'loans'                => TransactionLoanResource::collection($loans),
                'return_books'         => TransactionReturnBookResource::collection($returnBooks),
                'total_books'          => $user->hasAnyRole(['admin', 'operator']) ? Book::count() : 0,
                'total_users'          => $user->hasAnyRole(['admin', 'operator']) ? User::count() : 0,
                'total_loans'          => Loan::countForUser($user),
                'total_returned_books' => ReturnBook::countForUser($user),
                'unpaid_fines'         => $user->hasAnyRole(['member']) ? Fine::where('user_id', $user->id)->whereNot('payment_status', FinePaymentStatus::SUCCESS)->sum('total_fee') : 0,
            ]
        ]);
    }

    public function transactionChart(): array
    {
        $end_date = Carbon::now();
        $start_date = $end_date->copy()->subMonth();
        $user = Auth::user();

        $loans = Loan::selectRaw("DATE(loan_date) as date, COUNT(*) as loan")
            ->when(
                $user->hasAnyRole(['admin', 'operator']),
                function ($query) {
                    return $query;
                },
                function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }
            )
            ->whereBetween('loan_date', [$start_date->toDateString(), $end_date->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('loan', 'date');

        $return_book = ReturnBook::selectRaw("DATE(return_date) as date, COUNT(*) as returns")
            ->when(
                $user->hasAnyRole(['admin', 'operator']),
                function ($query) {
                    return $query;
                },
                function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }
            )
            ->whereNotNull('return_date')
            ->whereBetween('return_date', [$start_date->toDateString(), $end_date->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('returns', 'date');

        $charts = [];

        $period = Carbon::parse($start_date)->daysUntil($end_date);

        foreach ($period as $date) {
            $date_string = $date->toDateString();
            $charts[] = [
                'date'        => $date_string,
                'loan'        => $loans->get($date_string, 0),
                'return_book' => $return_book->get($date_string, 0)
            ];
        }

        return $charts;
    }
}
