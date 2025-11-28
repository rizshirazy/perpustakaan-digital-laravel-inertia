<?php

namespace App\Http\Resources\Admin;

use App\Traits\HasOptions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RouteAccessResource extends JsonResource
{
    use HasOptions;


    public function getOptions(): array
    {
        return [
            'roles' => Role::select(['id', 'name'])
                ->get()->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->name,
                ]),
            'permissions' => Permission::select(['id', 'name'])
                ->get()->map(fn($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->name,
                ]),
            'routes' => collect(Route::getRoutes())->map(fn($item) => [
                'value' => $item->getName(),
                'label' => $item->getName(),
            ])->filter(fn($item) => $item['value'] !== null)->values()->all(),
        ];
    }

    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'route_name' => $this->route_name,
            'role'       => RoleResource::make($this->whenLoaded('role')),
            'permission' => PermissionResource::make($this->whenLoaded('permission')),
            'created_at' => Carbon::parse($this->created_at)->isoFormat('DD MMMM YYYY HH:mm:ss'),
        ];
    }
}
