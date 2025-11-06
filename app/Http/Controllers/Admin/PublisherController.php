<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublisherRequest;
use App\Http\Resources\Admin\PublisherResource;
use App\Models\Publisher;
use App\Traits\HasFile;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    use HasFile;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publishers = Publisher::query()
            ->select(['id', 'name', 'slug', 'address', 'email', 'logo', 'phone'])
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['name', 'slug', 'email', 'address', 'phone']))
            ->paginate(request()->load ?? 10)
            ->withQueryString();


        return inertia('Admin/Publishers/Index', [
            'publishers' => PublisherResource::collection($publishers)->additional([
                'meta' => [
                    'has_pages' => $publishers->hasPages(),
                ]
            ]),
            'page_settings' => [
                'title'    => 'Penerbit',
                'subtitle' => 'Menampilkan semua penerbit yang terdaftar pada sistem.',
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
    public function create()
    {
        return inertia('Admin/Publishers/Create', [
            'page_settings' => [
                'title'    => 'Tambah Penerbit',
                'subtitle' => 'Halaman untuk menambahkan penerbit pada sistem.',
                'method'   => 'POST',
                'action'   => route('admin.publishers.store'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublisherRequest $request)
    {
        try {
            Publisher::create([
                'name'    => $name = $request->name,
                'slug'    => str()->lower(str()->slug($name) . '-' . str()->random(3)),
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
                'logo'    => $this->upload_file($request, 'logo', 'publishers'),
            ]);

            flashMessage(MessageType::CREATED->message('Penerbit'));

            return to_route('admin.publishers.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.publishers.index');
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
    public function edit(Publisher $publisher)
    {
        return inertia('Admin/Publishers/Edit', [
            'page_settings' => [
                'title'    => 'Edit Penerbit',
                'subtitle' => 'Halaman untuk memperbarui data penerbit.',
                'method'   => 'PUT',
                'action'   => route('admin.publishers.update', $publisher),
            ],
            'publisher' => $publisher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublisherRequest $request, Publisher $publisher)
    {
        try {
            $publisher->update([
                'name'    => $name = $request->name,
                'slug'    => $name !== $publisher->name ? str()->lower(str()->slug($name) . '-' . str()->random(3)) : $publisher->slug,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
                'logo'    => $this->update_file($request, $publisher, 'logo', 'publishers'),
            ]);

            flashMessage(MessageType::UPDATED->message('Penerbit'));

            return to_route('admin.publishers.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.publishers.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        try {
            $this->delete_file($publisher, 'logo');
            $publisher->delete();

            flashMessage(MessageType::DELETED->message('Penerbit'));

            return to_route('admin.publishers.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.publishers.index');
        }
    }
}
