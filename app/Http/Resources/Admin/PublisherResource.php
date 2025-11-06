<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PublisherResource extends JsonResource
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
            'name'       => $this->name,
            'slug'       => $this->slug,
            'address'    => $this->address,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'logo'       => $this->logo ? Storage::url($this->logo) : null,
            'created_at' => Carbon::parse($this->created_at)->isoFormat('D MMM YYYY'),
        ];
    }
}
