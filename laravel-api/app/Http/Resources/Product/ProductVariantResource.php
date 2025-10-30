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
            'name' => $this->name,
            'url' => $this->url,
            'code' => $this->code,
            'sku' => $this->sku,
            'stock' => (int) $this->stock,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'barcode' => $this->barcode,
            'attributeData' => $this->attribute_data,
            'productId' => $this->product_id,
            'attributes' => $this->whenLoaded(
                'variant_attributes',
                $this->variant_attributes->pluck('id')->toArray()
            ),
            // 'attributeNames' => $this->whenLoaded('variant_attributes', [
            //     'name' => $this->variant_attributes->pluck('name')->toArray()
            // ]),
            // 'attributeCatalogues' => $this->whenLoaded('variant_attributes', function (){
            //     return $this->variant_attributes->groupBy('attribute_catalogue_id')->map(function ($attrs, $catalogueId){
            //         return [
            //             'catalogueId' => $catalogueId,
            //             'attributeIds' => $attrs->pluck('id')->toArray(),
            //             'attributeNames' => $attrs->pluck('name')->toArray(),
            //         ];
            //     })->values();
            // }),
            'publish' => (int) $this->publish,
            'availableAgencies' => $this->whenLoaded('availableAgencies', function () {
                return $this->availableAgencies->map(function ($agency) {
                    return [
                        'id' => $agency->id,
                        'name' => $agency->name,
                        'slug' => $agency->slug,
                        'phone' => $agency->phone,
                        'address' => $agency->address,
                        'stock' => (int) $agency->pivot->stock,
                        'priceOverride' => (float) $agency->pivot->price_override,
                    ];
                });
            }),
        ];
    }
}
