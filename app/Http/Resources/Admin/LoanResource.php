<?php

namespace App\Http\Resources\Admin;

use App\Models\Book;
use App\Models\User;
use App\Traits\HasOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    use HasOptions;

    public function getOptions(): array
    {
        return [
            'books' => Book::select(['id', 'title'])
                ->whereHas('stock', fn($q) => $q->where('available', '>', 0))
                ->get()
                ->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->title
                ]),
            'users' => User::select(['id', 'name'])
                ->get()
                ->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->name
                ])
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'loan_code'       => $this->loan_code,
            'loan_date'       => [
                'raw'       => $this->loan_date,
                'formatted' => $this->loan_date->isoFormat('DD MMM YYYY')
            ],
            'due_date'        => [
                'raw'       => $this->due_date,
                'formatted' => $this->due_date->isoFormat('DD MMM YYYY')
            ],
            'has_return_book' => $this->returnBook()->exists(),
            'user'            => $this->whenLoaded('user', [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'book'            => $this->whenLoaded('book', [
                'id'    => $this->book?->id,
                'title' => $this->book?->title,
                'slug'  => $this->book?->slug,
            ]),
        ];
    }
}
