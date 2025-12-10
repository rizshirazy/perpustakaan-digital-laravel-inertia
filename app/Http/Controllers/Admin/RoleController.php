<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\Admin\AssignPermissionResource;
use App\Http\Resources\Admin\AssignUserResource;
use App\Http\Resources\Admin\RoleResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('password.confirm', except: ['store', 'update', 'updateAssignPermission', 'updateAssignUser'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $roles = Role::query()->select('id', 'name', 'guard_name', 'created_at')
            ->when(request()->search, function ($query, $search) {
                $query->whereAny(['name', 'guard_name'], 'ILIKE', '%' . $search . '%');
            })->when(request()->field && request()->direction, fn($q) => $q->orderBy(request()->field, request()->direction))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/Roles/Index', [
            'roles' => RoleResource::collection($roles)->additional([
                'meta' => [
                    'has_pages' => $roles->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Peran',
                'subtitle' => 'Halaman untuk mengelola data peran pada sistem.',
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Admin/Roles/Create', [
            'page_settings' => [
                'title'    => 'Buat Peran Baru',
                'subtitle' => 'Halaman untuk membuat peran baru pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.roles.store'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        try {
            Role::create([
                'name'       => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            flashMessage(MessageType::CREATED->message('Peran'));

            return redirect()->route('admin.roles.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.roles.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): Response
    {
        return inertia('Admin/Roles/Edit', [
            'page_settings' => [
                'title'    => 'Edit Peran',
                'subtitle' => 'Halaman untuk mengedit peran pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.roles.update', $role),
            ],
            'role' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        try {
            $role->update([
                'name'       => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            flashMessage(MessageType::UPDATED->message('Peran'));

            return redirect()->route('admin.roles.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.roles.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        Log::info('Destroy called', [
            'role' => $role ? $role->getAttributes() : null,
            'role_type' => is_object($role) ? get_class($role) : gettype($role),
            'config_role' => config('permission.models.role'),
        ]);

        try {
            $role->delete();

            flashMessage(MessageType::DELETED->message('Peran'));

            return redirect()->route('admin.roles.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.roles.index');
        }
    }

    public function indexAssignPermission(): Response
    {
        $roles = Role::query()->select('id', 'name', 'created_at')
            ->when(request()->search, function ($query, $search) {
                $query->where('name', 'ILIKE', '%' . $search . '%');
            })->when(request()->field && request()->direction, fn($q) => $q->orderBy(request()->field, request()->direction))
            ->with('permissions')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/AssignPermissions/Index', [
            'roles' => AssignPermissionResource::collection($roles)->additional([
                'meta' => [
                    'has_pages' => $roles->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Tetapkan Izin',
                'subtitle' => 'Halaman untuk mengelola data tetapkan izin yang tersedia pada sistem.',
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10,
            ]
        ]);
    }

    public function editAssignPermission(Role $role): Response
    {
        $role->load('permissions');

        return inertia('Admin/AssignPermissions/Edit', [
            'page_settings' => [
                'title'    => 'Sikronisasi Izin',
                'subtitle' => 'Halaman untuk mengedit sinkronisasi izin pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.assign-permissions.update', $role),
            ],
            'page_data' => AssignPermissionResource::withOptions($role)
        ]);
    }

    public function updateAssignPermission(AssignPermissionRequest $request, Role $role): RedirectResponse
    {
        try {
            $role->syncPermissions($request->permissions ?? []);

            flashMessage("Berhasil sinkronisasi izin ke peran {$role->name}");

            return redirect()->route('admin.assign-permissions.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.assign-permissions.index');
        }
    }

    public function indexAssignUser(): Response
    {
        $users = User::query()->select('id', 'name', 'username', 'email')
            ->when(request()->search, function ($query, $search) {
                $query->whereAny(['name', 'username', 'email'], 'ILIKE', '%' . $search . '%');
            })->when(request()->field && request()->direction, fn($q) => $q->orderBy(request()->field, request()->direction))
            ->with('roles')
            ->latest('created_at')
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/AssignUsers/Index', [
            'users' => AssignUserResource::collection($users)->additional([
                'meta' => [
                    'has_pages' => $users->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Tetapkan Peran',
                'subtitle' => 'Halaman untuk mengelola data tetapkan peran yang tersedia pada sistem.',
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10,
            ]
        ]);
    }

    public function editAssignUser(User $user): Response
    {
        $user->load('roles');

        return inertia('Admin/AssignUsers/Edit', [
            'page_settings' => [
                'title'    => 'Sikronisasi Peran',
                'subtitle' => 'Halaman untuk mengedit sinkronisasi peran pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.assign-users.update', $user),
            ],
            'page_data' => AssignUserResource::withOptions($user)
        ]);
    }

    public function updateAssignUser(AssignPermissionRequest $request, User $user): RedirectResponse
    {
        try {
            $user->syncRoles($request->roles ?? []);

            flashMessage("Berhasil sinkronisasi peran ke pengguna {$user->name}");
            return redirect()->route('admin.assign-users.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.assign-users.index');
        }
    }
}
