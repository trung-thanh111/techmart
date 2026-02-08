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
            'price' => (float) $this->price,
            'barcode' => $this->barcode,
            'attributeData' => $this->attribute_data,
            'productId' => $this->product_id,
            // 'products' => $this->whenLoaded('products', function () {
            //     return new ProductResource($this->products);
            // }),
            'attributes' => $this->whenLoaded(
                'variant_attributes',
                $this->variant_attributes->pluck('id')->toArray()
            ),
            'agencies' => $this->whenLoaded('agencies', function () {
                return $this->agencies->map(function ($agency) {
                    return [
                        'id' => $agency->id,
                        'name' => $agency->name,
                        'phone' => $agency->phone,
                        'address' => $agency->address,
                    ];
                });
            }),
            'publish' => (int) $this->publish,
        ];
    }
}
