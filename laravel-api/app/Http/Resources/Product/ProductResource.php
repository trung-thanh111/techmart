<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Brand\BrandResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'content' => $this->content,
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'price' => price_to_string($this->price),
            'made_in' => $this->made_in,
            'icon' => $this->icon,
            'album' => $this->album,
            'extraAlbum' => $this->extra_album,
            'order' => $this->order,
            'productCatalogueId' => $this->product_catalogue_id,
            'brandId' => $this->brand_id,
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
            'index' => $this->index,
            'scheme' => $this->scheme,
            'iframe' => $this->iframe,
            'publish' => $this->publish,
            'creator' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            'productVariant' => ProductVariantResource::collection($this->product_variants),
            'productCatalogue' => ProductCatalogueResource::collection($this->product_catalogues),
            'brand' => new BrandResource($this->brands), // quan hệ 1-1 belongSto

            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'createdAtHuman' => $this->created_at?->diffForHumans(),
            'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
