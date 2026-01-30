<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'slug' => $this->slug,
            'email' => $this->email,
            'description' => $this->description,
            'logo' => [
                'path' => $this->logo ?? null,
                'url' => asset("/storage/{$this->logo}") ?? null,
            ],
            'website' => $this->website,
            'country' => $this->country,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            'product_count' => (int) ($this->product_counts_count ?? 0),
            // 'creator' => $this->whenLoaded('users', [
            //     'id' => $this->users->id,
            //     'name' => $this->users->name,
            // ]),
            // 'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            // 'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            // 'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            // 'createdAtHuman' => $this->created_at?->diffForHumans(),
            // 'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
