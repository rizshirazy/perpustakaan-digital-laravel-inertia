<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => (string) $this->id,
            'loan_code' => $this->loan_code,
            'book'        => [
                'id'    => $this->book?->id,
                'title' => $this->book?->title,
            ],
        ];
    }
}
