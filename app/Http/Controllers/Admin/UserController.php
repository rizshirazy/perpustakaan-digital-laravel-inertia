<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Traits\HasFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Throwable;

class UserController extends Controller
{
    use HasFile;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {

        $users = User::query()
            ->select(['id', 'name', 'username', 'email', 'phone', 'avatar', 'gender', 'date_of_birth', 'address', 'created_at'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10)
            ->withQueryString();

        return inertia('Admin/Users/Index', [
            'users' => UserResource::collection($users)->additional([
                'meta' => [
                    'has_pages' => $users->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Pengguna',
                'subtitle' => 'Menampilkan semua pengguna yang terdaftar pada sistem.',
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
        return inertia('Admin/Users/Create', [
            'page_settings' => [
                'title'    => 'Tambah Penguna',
                'subtitle' => 'Halaman untuk menambahkan pengguna baru pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.users.store'),
            ],
            'page_data' => UserResource::onlyOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        try {
            User::create(
                [
                    'name'          => $request->name,
                    'username'      => usernameGenerator($request->name),
                    'address'       => $request->address,
                    'password'      => Hash::make($request->password),
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    'gender'        => $request->gender,
                    'avatar'        => $this->upload_file($request, 'avatar', 'users'),
                ]
            );

            flashMessage(MessageType::CREATED->message('Pengguna'));

            return to_route('admin.users.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.users.index');
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
    public function edit(User $user): Response
    {
        return inertia('Admin/Users/Edit', [
            'page_settings' => [
                'title'    => 'Edit Pengguna',
                'subtitle' => 'Halaman untuk memperbarui data pengguna pada sistem.',
                'method'   => 'PUT',
                'action'   => route('admin.users.update', $user),
            ],
            'page_data' => UserResource::withOptions($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        try {
            $user->update(
                [
                    'name'          => $request->name,
                    'address'       => $request->address,
                    'password'      => Hash::make($request->password),
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    'gender'        => $request->gender,
                    'avatar'        => $this->update_file($request, $user, 'avatar', 'users'),
                ]
            );

            flashMessage(MessageType::UPDATED->message('Pengguna'));

            return to_route('admin.users.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.users.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->delete_file($user, 'avatar');
            $user->delete();

            flashMessage(MessageType::DELETED->message('Pengguna'));

            return to_route('admin.users.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.users.index');
        }
    }
}
