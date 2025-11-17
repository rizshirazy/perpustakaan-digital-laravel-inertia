<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserGender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $password_confirmation
 * @property string|null $phone
 * @property string|null $gender
 * @property string|null $date_of_birth
 * @property string|null $address
 * @property \Illuminate\Http\UploadedFile|null $avatar
 */
class UserRequest extends FormRequest
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
            'name'          => ['required', 'min:3', 'max:255', 'string'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password'      => [
                Rule::when($this->routeIs('admin.users.store'), ['required', 'min:8', 'max:255', 'confirmed']),
                Rule::when($this->routeIs('admin.users.update'), ['nullable', 'min:8', 'max:255', 'confirmed'])
            ],
            'phone'         => ['nullable', 'min:10', 'max:15'],
            'avatar'        => ['nullable', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'gender'        => ['nullable', Rule::enum(UserGender::class)],
            'date_of_birth' => ['nullable', 'date'],
            'address'       => ['nullable', 'string', 'min:3', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'name'          => 'Nama',
            'email'         => 'Email',
            'password'      => 'Password',
            'phone'         => 'Nomor Telepon',
            'avatar'        => 'Avatar',
            'gender'        => 'Jenis Kelamin',
            'date_of_birth' => 'Tanggal Lahir',
            'address'       => 'Alamat',
        ];
    }
}
