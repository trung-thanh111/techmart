<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'productVariantId' => (int) $this->product_variant_id,
            'agencyId' => (int) $this->agency_id,
            'stock' => (int) $this->stock,
            'priceOverride' => $this->price_override,
            'publish' => (int) $this->publish,
            'productVariant' => $this->product_variants
                ? [
                    'id' => $this->product_variants->id,
                    'name' => $this->product_variants->name,
                    'productId' => $this->product_variants->product_id,
                ] : null,
            'agency' => $this->agencies
                ? [
                    'id' => $this->agencies->id,
                    'name' => $this->agencies->name,
                ] : null,
        ];
    }
}
