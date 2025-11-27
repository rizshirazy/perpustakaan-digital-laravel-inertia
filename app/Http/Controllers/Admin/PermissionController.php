<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;
use App\Http\Resources\Admin\PermissionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $permissions = Permission::query()->select('id', 'name', 'guard_name', 'created_at')
            ->when(request()->search, function ($query, $search) {
                $query->whereAny(['name', 'guard_name'], 'ILIKE', '%' . $search . '%');
            })->when(request()->field && request()->direction, fn($q) => $q->orderBy(request()->field, request()->direction))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/Permissions/Index', [
            'permissions' => PermissionResource::collection($permissions)->additional([
                'meta' => [
                    'has_pages' => $permissions->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Izin',
                'subtitle' => 'Halaman untuk mengelola data izin pada sistem.',
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
        return inertia('Admin/Permissions/Create', [
            'page_settings' => [
                'title'    => 'Buat Izin Baru',
                'subtitle' => 'Halaman untuk membuat izin baru pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.permissions.store'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request): RedirectResponse
    {
        try {
            Permission::create([
                'name'       => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            flashMessage(MessageType::CREATED->message('Izin'));

            return redirect()->route('admin.permissions.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.permissions.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return inertia('Admin/Permissions/Edit', [
            'page_settings' => [
                'title'    => 'Edit Izin',
                'subtitle' => 'Halaman untuk mengedit izin pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.permissions.update', $permission),
            ],
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        try {
            $permission->update([
                'name'       => $request->name,
                'guard_name' => $request->guard_name,
            ]);

            flashMessage(MessageType::UPDATED->message('Izin'));

            return redirect()->route('admin.permissions.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.permissions.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        try {
            $permission->delete();

            flashMessage(MessageType::DELETED->message('Izin'));

            return redirect()->route('admin.permissions.index');
        } catch (\Throwable $e) {

            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return redirect()->route('admin.permissions.index');
        }
    }
}
