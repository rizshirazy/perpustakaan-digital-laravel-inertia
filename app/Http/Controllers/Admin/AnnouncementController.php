<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Http\Resources\Admin\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $announcements = Announcement::select([
            'id',
            'message',
            'url',
            'is_active'
        ])
            ->paginate(10)
            ->withQueryString();

        return inertia('Admin/Announcements/Index', [
            'page_settings' => [
                'title'    => 'Pengumuman',
                'subtitle' => 'Menampilkan semua data pengumuman yang tersimpan pada sistem.'
            ],
            'announcements' => AnnouncementResource::collection($announcements)->additional([
                'meta' => [
                    'has_pages' => $announcements->hasPages()
                ]
            ])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Admin/Announcements/Create', [
            'page_settings' => [
                'title'    => 'Tambah Pengumuman',
                'subtitle' => 'Halaman untuk membuat pengumuman baru',
                'method'   => 'POST',
                'action'   => route('admin.announcements.store')
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementRequest $request): RedirectResponse
    {
        try {
            if ($request->is_active) {
                Announcement::where('is_active', true)->update(['is_active' => false]);
            }

            Announcement::create([
                'message'   => $request->message,
                'url'       => $request->url,
                'is_active' => $request->is_active,
            ]);

            flashMessage(MessageType::CREATED->message('Pengumuman'));

            return to_route('admin.announcements.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.announcements.index');
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
    public function edit(Announcement $announcement): Response
    {
        return inertia('Admin/Announcements/Edit', [
            'page_settings' => [
                'title'    => 'Edit Pengumuman',
                'subtitle' => 'Halaman untuk memperbarui pengumuman',
                'method'   => 'PUT',
                'action'   => route('admin.announcements.update', $announcement)
            ],
            'announcement' => $announcement
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        try {
            if ($request->is_active) {
                Announcement::where('is_active', true)
                    ->where('id', '!=', $announcement->id)
                    ->update(['is_active' => false]);
            }

            $announcement->update([
                'message'   => $request->message,
                'url'       => $request->url,
                'is_active' => $request->is_active,
            ]);

            flashMessage(MessageType::UPDATED->message('Pengumuman'));

            return to_route('admin.announcements.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.announcements.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        try {
            $announcement->delete();

            flashMessage(MessageType::DELETED->message('Pengumuman'));

            return to_route('admin.announcements.index');
        } catch (\Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');

            return to_route('admin.announcements.index');
        }
    }
}
