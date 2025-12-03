<?php

namespace App\Actions;

use App\Models\ReturnBook;
use Carbon\Carbon;

class GenerateReturnBookCode
{
    public function __invoke(): string
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
}
