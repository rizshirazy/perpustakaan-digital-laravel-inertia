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
            'id'          => $this->id,
            'return_code' => $this->return_code,
            'loan'        => $this->whenLoaded('loan', [
                'id'        => $this->loan?->id,
                'loan_code' => $this->loan?->loan_code,
                'loan_date' => [
                    'raw'       => $this->loan?->loan_date,
                    'formatted' => $this->loan?->loan_date->isoFormat('DD MMM YYY')
                ],
                'due_date' => [
                    'raw'       => $this->loan?->due_date,
                    'formatted' => $this->loan?->due_date->isoFormat('DD MMM YYY')
                ]
            ]),
            'book'             => $this->whenLoaded('book', [
                'id'    => $this->book?->id,
                'title' => $this->book?->title,
                'slug'  => $this->book?->slug,
            ]),
            'user'             => $this->whenLoaded('user', [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'status'           => [
                'value' => $this->status->value,
                'label' => $this->status->label()
            ],
            'fine'              => $this->whenLoaded('fine', $this->fine?->total_fee),
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
