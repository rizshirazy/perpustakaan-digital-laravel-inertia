<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => (string) $this->id,
            'late_fee'       => $this->late_fee,
            'other_fee'      => $this->other_fee,
            'total_fee'      => $this->total_fee,
            'fine_date'      => $this->fine_date,
            'payment_status' => [
                'value' => $this->payment_status->value,
                'label' => $this->payment_status->label(),
            ],
            'loan' => $this->when(
                $this->relationLoaded('returnBook') && $this->returnBook->relationLoaded('loan'),
                fn() => LoanResource::make($this->returnBook->loan)
            ),
            'return_book' => $this->when(
                $this->relationLoaded('returnBook'),
                function () {
                    $returnBook = clone $this->returnBook;

                    $returnBook->unsetRelation('fine');

                    return ReturnBookResource::make($returnBook);
                }
            ),
        ];
    }
}
