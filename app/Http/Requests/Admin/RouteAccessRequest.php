<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RouteAccessRequest extends FormRequest
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
            'route_name'    => ['required', 'string', 'max:255'],
            'role_id'       => ['nullable', 'exists:roles,id'],
            'permission_id' => ['nullable', 'exists:permissions,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'route_name'    => 'Route',
            'role_id'       => 'Peran',
            'permission_id' => 'Izin',
        ];
    }
}
