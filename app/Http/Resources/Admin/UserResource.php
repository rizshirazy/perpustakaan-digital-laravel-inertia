<?php

namespace App\Http\Resources\Admin;

use App\Enums\UserGender;
use App\Traits\HasOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    use HasOptions;

    public function getOptions(): array
    {
        return [
            'genders' => UserGender::options(),
        ];
    }

    public function toArray(Request $request): array
    {
        $dateOfBirth = optional($this->date_of_birth);

        return [
            'id'            => (string) $this->id,
            'name'          => $this->name,
            'username'      => $this->username,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'avatar'        => $this->avatar ? Storage::url($this->avatar) : null,
            'date_of_birth' => [
                'raw'       => $dateOfBirth?->toDateString(),
                'formatted' => $dateOfBirth?->isoFormat('D MMM YYYY'),
            ],
            'gender'        =>
            [
                'value'     => $this->gender?->value,
                'label'     => $this->gender?->label()
            ],
            'created_at'    =>
            [
                'raw'       => $this->created_at,
                'formatted' => Carbon::parse($this->created_at)->isoFormat('D MMM YYYY')
            ],

        ];
    }
}
