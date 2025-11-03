<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Method này dùng cho single item (show, store, update)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variant = $this->whenLoaded('product_variants');
        $product = $variant?->products;

        return [
            'id' => $this->id,
            'userId' => (int) $this->user_id,
            'productVariantId' => (int) $this->product_variant_id,
            "products" => [
                'id' => $product?->id,
                'name' => $product?->name,
                'slug' => $product?->slug,
                'priceOrigin' => (float) ($product->price_origin ?? 0),
                'album' => $product->album,
                'productCatalogueId' => (int) ($product->product_catalogue_id ?? 0),
                'brandId' => (int) ($product->brand_id ?? 0),
                'variant' => [
                    'id' => $variant?->id,
                    'name' => $variant?->name,
                    'price' => $variant?->price,
                    "attributeData" => $variant?->attribute_data
                ],
            ],
        ];
    }
}
