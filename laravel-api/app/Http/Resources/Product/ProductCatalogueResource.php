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

        if ($request->boolean('simple', false)) {
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
            ];
        }
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
            "products" => $this->whenLoaded('products', function () {
                return ProductResource::collection($this->products);
            }),
            'order' => (int) $this->order,
            'isHot' => (bool) $this->is_hot,
            'publish' => (int) $this->publish,
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
        ];
    }
}
