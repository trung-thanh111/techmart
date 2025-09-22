<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCatalogueResource extends JsonResource
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
            'parentId' => (int) $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'order' => (int) $this->order,
            'isHot' => (bool) $this->is_hot,
            'publish' => (int) $this->publish,
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
            // 'index' => $this->index,
            // 'scheme' => $this->scheme,
            // 'iframe' => $this->iframe,
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
