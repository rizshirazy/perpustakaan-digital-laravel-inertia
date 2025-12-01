<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LoanStatisticResource;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Response;

class LoanStatisticController extends Controller
{
    public function index(): Response
    {
        return inertia('Admin/LoanStatistics/Index', [
            'page_settings' => [
                'title'    => 'Statistik Peminjaman',
                'subtitle' => 'Menampilkan statistik peminjaman buku pada sistem.',
            ],
            'page_data' => [
                'least_loaned_books' => LoanStatisticResource::collection(Book::leastLoanedBooks()),
                'most_loaned_books'  => LoanStatisticResource::collection(Book::mostLoanedBooks()),
                'total_loans'        => Loan::totalLoanBooks(),
            ]
        ]);
    }
}
