<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'message'    => $this->message,
            'url'        => $this->url,
            'is_active'  => $this->is_active ? 'Aktif' : 'Tidak Aktif',
            'created_at' => Carbon::parse($this->created_at)->isoFormat('D MMM YYYY')
        ];
    }
}
