<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RouteAccessRequest;
use App\Http\Resources\Admin\RouteAccessResource;
use App\Models\RouteAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Response;

class RouteAccessController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('password.confirm', except: ['store', 'update'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $route_accesses = RouteAccess::query()
            ->select(['id', 'route_name', 'role_id', 'permission_id', 'created_at'])
            ->filter(request()->only(['search']))
            ->with(['role', 'permission'])
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/RouteAccesses/Index', [
            'route_accesses' => RouteAccessResource::collection($route_accesses)->additional([
                'meta' => [
                    'has_pages' => $route_accesses->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Akses Rute',
                'subtitle' => 'Menampilkan semua akses rute pada sistem.',
            ],
            'state' => [
                'page'   => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load'   => 10
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Admin/RouteAccesses/Create', [
            'page_settings' => [
                'title'    => 'Tambah Akses Rute',
                'subtitle' => 'Halaman untuk membuat akses rute pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.route-accesses.store'),
            ],
            'page_data' => RouteAccessResource::onlyOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RouteAccessRequest $request): RedirectResponse
    {
        try {
            RouteAccess::create([
                'route_name'    => $request->route_name,
                'role_id'       => $request->role_id,
                'permission_id' => $request->permission_id,
            ]);

            flashMessage(MessageType::CREATED->message('Akses Rute'));

            return to_route('admin.route-accesses.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.route-accesses.index');
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
    public function edit(RouteAccess $routeAccess): Response
    {
        $routeAccess->load(['role', 'permission']);

        return inertia('Admin/RouteAccesses/Edit', [
            'page_settings' => [
                'title'    => 'Edit Akses Rute',
                'subtitle' => 'Halaman untuk mengedit akses rute pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.route-accesses.update', $routeAccess->id),
            ],
            'page_data' => RouteAccessResource::withOptions($routeAccess),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RouteAccessRequest $request, RouteAccess $routeAccess): RedirectResponse
    {
        try {
            $routeAccess->update([
                'route_name'    => $request->route_name,
                'role_id'       => $request->role_id,
                'permission_id' => $request->permission_id,
            ]);

            flashMessage(MessageType::UPDATED->message('Akses Rute'));

            return to_route('admin.route-accesses.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.route-accesses.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RouteAccess $routeAccess)
    {
        try {
            $routeAccess->delete();

            flashMessage(MessageType::DELETED->message('Akses Rute'));

            return to_route('admin.route-accesses.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.route-accesses.index');
        }
    }
}
