<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
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
            'total'     => ['required', 'integer', 'min:0'],
            'available' => ['required', 'integer', 'min:0'],
            'loaned'    => ['required', 'integer', 'min:0'],
            'lost'      => ['required', 'integer', 'min:0'],
            'damaged'   => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'total'     => 'Total Stok',
            'available' => 'Jumlah Tersedia',
            'loaned'    => 'Jumlah Dipinjam',
            'lost'      => 'Jumlah Hilang',
            'damaged'   => 'Jumlah Rusak',
        ];
    }
}
