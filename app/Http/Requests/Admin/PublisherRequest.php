<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string|null $email
 * @property string|null $address
 * @property string|null $phone
 * @property \Illuminate\Http\UploadedFile|null $logo
 */
class PublisherRequest extends FormRequest
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
            'name'    => ['required', 'min:3', 'max:100', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:15'],
            'logo'    => ['nullable', 'mimes:png,jpe,jpeg,webp', 'max:2048']
        ];
    }

    public function attributes(): array
    {
        return [
            'name'    => 'Nama',
            'address' => 'Alamat',
            'email'   => 'Email',
            'phone'   => 'Nomor Telepon',
            'logo'    => 'Logo',
        ];
    }
}
