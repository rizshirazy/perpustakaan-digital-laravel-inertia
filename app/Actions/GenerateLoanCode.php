<?php

namespace App\Actions;

use App\Models\Loan;
use Carbon\Carbon;

class GenerateLoanCode
{
    public function __invoke(): string
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
