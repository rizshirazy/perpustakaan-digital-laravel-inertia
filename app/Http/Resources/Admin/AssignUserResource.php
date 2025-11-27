<?php

namespace App\Http\Resources\Admin;

use App\Traits\HasOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class AssignUserResource extends JsonResource
{

    use HasOptions;

    public function getOptions(): array
    {
        return
            [
                'roles' => Role::query()->select(['id', 'name'])
                    ->get()->map(fn($item) => [
                        'value' => $item->name,
                        'label' => $item->name,
                    ])->values()->all(),
            ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,
            'roles'    => $this->getRoleNames(),
        ];
    }
}
