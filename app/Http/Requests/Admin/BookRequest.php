<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $title
 * @property string $author
 * @property string $isbn
 * @property string $language
 * @property string $synopsis
 * @property string $category_id
 * @property string $publisher_id
 * @property string|null $synopsis
 * @property integer $publication_year
 * @property integer $number_of_pages
 * @property integer $total
 * @property integer $price
 * @property \Illuminate\Http\UploadedFile|null $cover
 */
class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'            => ['required', 'min:3', 'max:255', 'string'],
            'author'           => ['required', 'min:3', 'max:255', 'string'],
            'publication_year' => ['required', 'numeric', 'integer'],
            'isbn'             => ['required', 'string', 'max:255'],
            'language'         => ['required', Rule::enum(BookLanguage::class)],
            'synopsis'         => ['nullable'],
            'number_of_pages'  => ['required', 'numeric', 'integer'],
            'cover'            => ['nullable', 'mimes:png,jpe,jpeg,webp', 'max:2048'],
            'price'            => ['required', 'numeric', 'min:0'],
            'total'            => ['required', 'numeric', 'min:0'],
            'category_id'      => ['required', 'exists:categories,id'],
            'publisher_id'     => ['required', 'exists:publishers,id'],
        ];
    }

    public function attributes()
    {
        return [
            'title'            => 'Judul',
            'author'           => 'Penulis',
            'publication_year' => 'Tahun Terbit',
            'isbn'             => 'ISBN',
            'language'         => 'Bahasa',
            'synopsis'         => 'Sinopsis',
            'number_of_pages'  => 'Jumlah Halaman',
            'status'           => 'Status',
            'cover'            => 'Cover',
            'price'            => 'Harga',
            'total'            => 'Stok',
            'category_id'      => 'Kategori',
            'publisher_id'     => 'Penerbit',
        ];
    }
}
