<?php

namespace App\Http\Resources\Admin;

use App\Enums\BookLanguage;
use App\Enums\BookStatus;
use App\Models\Category;
use App\Models\Publisher;
use App\Traits\HasOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
{
    use HasOptions;

    public function getOptions(): array
    {
        return
            [
                'languages' => BookLanguage::options(),
                'categories' => Category::orderBy('name')->get()->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->name,
                ])->values()->all(),
                'publishers' => Publisher::orderBy('name')->get()->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->name,
                ])->values()->all(),
            ];
    }


    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'book_code'        => $this->book_code,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'book_code'        => $this->book_code,
            'author'           => $this->author,
            'publication_year' => (string) $this->publication_year,
            'isbn'             => $this->isbn,
            'language'         => [
                'value' => $this->language->value,
                'label' => $this->language->label()
            ],
            'synopsis'         => $this->synopsis,
            'number_of_pages'  => $this->number_of_pages,
            'status'           => $this->status,
            'cover'            => $this->cover ? Storage::url($this->cover) : null,
            'price'            => number_format($this->price, 0, ",", "."),
            'created_at'       => Carbon::parse($this->created_at)->isoFormat('D MMM YYYY'),
            'category'         => [
                'id'   => (string) $this->category_id,
                'name' => $this->category?->name,
            ],
            'publisher'        => [
                'id'   => (string) $this->publisher_id,
                'name' => $this->publisher?->name,
            ],
            'stock'            => [
                'total'     => $this->stock?->total,
                'available' => $this->stock?->available,
                'loaned'    => $this->stock?->loaned,
                'lost'      => $this->stock?->lost,
                'damaged'   => $this->stock?->damaged,
            ]
        ];
    }
}
