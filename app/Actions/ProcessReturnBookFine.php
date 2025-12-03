<?php

namespace App\Actions;

use App\Enums\ReturnBookCondition;
use App\Enums\ReturnBookStatus;
use App\Models\FineSetting;
use App\Models\ReturnBook;
use App\Models\ReturnBookCheck;
use Carbon\Carbon;

class ProcessReturnBookFine
{
    public function __invoke(ReturnBook $returnBook, ReturnBookCheck $returnBookCheck, FineSetting $fineSetting, int $daysLate): ?array
    {
        $lateFee = $fineSetting->late_fee_per_day * $daysLate;

        switch ($returnBookCheck->condition->value) {
            case ReturnBookCondition::DAMAGED->value:
                $otherFee = ($fineSetting->damage_fee_percentage / 100) * $returnBook->book->price;

                $returnBook->update([
                    'status' => ReturnBookStatus::FINE->value
                ]);

                $this->createFine($returnBook, $lateFee, $otherFee);

                return [
                    'message' => 'Kondisi buku rusak, harus membayar denda kerusakan',
                ];

            case ReturnBookCondition::LOST->value:
                $otherFee = ($fineSetting->lost_fee_percentage / 100) * $returnBook->book->price;

                $returnBook->update([
                    'status' => ReturnBookStatus::FINE->value
                ]);

                $this->createFine($returnBook, $lateFee, $otherFee);

                return [
                    'message' => 'Kondisi buku hilang, harus membayar denda kehilangan buku',
                ];

            default:
                if ($daysLate > 0) {
                    $returnBook->update([
                        'status' => ReturnBookStatus::FINE->value
                    ]);

                    $this->createFine($returnBook, $lateFee, 0);

                    return [
                        'message' => 'Terlambat mengembalikan buku, harus membayar denda keterlambatan',
                    ];
                }

                $returnBook->update([
                    'status' => ReturnBookStatus::RETURNED->value
                ]);

                return null;
        }
    }

    private function createFine(ReturnBook $returnBook, float $lateFee, float $otherFee): void
    {
        $returnBook->fine()->create([
            'user_id'   => $returnBook->user_id,
            'late_fee'  => $lateFee,
            'other_fee' => $otherFee,
            'total_fee' => $lateFee + $otherFee,
            'fine_date' => Carbon::today(),
        ]);
    }
}
