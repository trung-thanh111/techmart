<?php
namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'album' => $this->album,
            'code' => $this->code,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'price' => price_to_string($this->price),
            'barcode' => $this->barcode,
            'productId' => $this->product_id,
            'attributes' => $this->whenLoaded('variant_attributes', [
                'id' => $this->variant_attributes->pluck('id')->toArray()
            ]),
            'attributeNames' => $this->whenLoaded('variant_attributes', [
                'name' => $this->variant_attributes->pluck('name')->toArray()
            ]),
            'attributeCatalogues' => $this->whenLoaded('variant_attributes', function (){
                return $this->variant_attributes->groupBy('attribute_catalogue_id')->map(function ($attrs, $catalogueId){
                    return [
                        'catalogueId' => $catalogueId,
                        'attributeIds' => $attrs->pluck('id')->toArray(),
                        'attributeNames' => $attrs->pluck('name')->toArray(),
                    ];
                })->values();
            }),
            'publish' => $this->publish,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
