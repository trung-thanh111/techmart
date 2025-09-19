<?php

namespace App\Http\Resources\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'type' => $this->type,
            'position' => (int) $this->position,
            'device' => $this->device,
            'display_mode' => $this->display_mode,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'sort_order' => $this->sort_order,
            'link_url' => $this->link_url,
            'extra_data' => $this->extra_data,
            'publish' => (int) $this->publish,
            // 'creator' => $this->whenLoaded('users', [
            //     'id' => $this->users->id,
            //     'name' => $this->users->name,
            // ]),
            // 'createdAt' => $this->created_at ?? null,
            // 'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
