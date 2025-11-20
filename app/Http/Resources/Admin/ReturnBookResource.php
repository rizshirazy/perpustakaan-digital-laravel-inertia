<?php

namespace App\Http\Resources\Admin;

use App\Enums\ReturnBookCondition;
use App\Traits\HasOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnBookResource extends JsonResource
{
    use HasOptions;

    public function getOptions(): array
    {
        return [
            'conditions' => ReturnBookCondition::options(),
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'               => (string) $this->id,
            'return_code'      => $this->return_code,
            'loan'             => LoanResource::make($this->whenLoaded('loan')),
            'book'             => BookResource::make($this->whenLoaded('book')),
            'user'             => UserResource::make($this->whenLoaded('user')),
            'status'           => [
                'value' => $this->status?->value,
                'label' => $this->status?->label()
            ],
            'fine'              => FineResource::make($this->whenLoaded('fine')),
            'return_book_check' => $this->whenLoaded('returnBookCheck', [
                'condition' => [
                    'value' => $this->returnBookCheck?->condition->value,
                    'label' => $this->returnBookCheck?->condition->label(),
                ]
            ]),
            'return_date'      => [
                'raw'       => $this->return_date,
                'formatted' => $this->return_date?->isoFormat('DD MMM YYYY'),
            ],
        ];
    }
}
