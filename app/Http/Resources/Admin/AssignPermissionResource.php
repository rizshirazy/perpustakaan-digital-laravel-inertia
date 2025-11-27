<?php

namespace App\Http\Resources\Admin;

use App\Traits\HasOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

class AssignPermissionResource extends JsonResource
{
    use HasOptions;

    public function getOptions(): array
    {
        return
            [
                'permissions' => Permission::query()->select(['id', 'name'])
                    ->where('guard_name', $this->guard_name)->get()->map(fn($item) => [
                        'value' => $item->name,
                        'label' => $item->name,
                    ])->values()->all(),
            ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'guard_name'  => $this->guard_name,
            'permissions' => $this->getPermissionNames(),
        ];
    }
}
