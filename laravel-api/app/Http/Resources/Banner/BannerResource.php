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
            'album' => $this->album,
            'type' => $this->type,
            'position' => $this->position,
            'startAt' => $this->start_at?->format('Y-m-d H:i:s'),
            'endAt' => $this->end_at?->format('Y-m-d H:i:s'),
            'order' => (int) $this->order,
            'isHot' => (bool) $this->is_hot,
            'linkUrl' => $this->link_url,
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
