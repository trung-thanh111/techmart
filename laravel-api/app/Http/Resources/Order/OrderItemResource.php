<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'orderId' => $this->order_id,
            'productId' => $this->product_id,
            'productVariantId' => $this->product_variant_id,
            'productSku' => $this->product_sku,
            'productName' => $this->product_name,
            'productDescription' => $this->product_description,
            'productAttributes' => $this->product_attributes,
            'productImage' => (function () {
                $rawImage = $this->product_image;
                if (empty($rawImage) && $this->product_id) {
                    $prod = $this->relationLoaded('products') ? $this->products : \App\Models\Product::find($this->product_id);
                    if ($prod && !empty($prod->album)) {
                        $first = is_array($prod->album) ? ($prod->album[0] ?? null) : $prod->album;
                        $rawImage = is_array($first) ? ($first['url'] ?? $first['path'] ?? null) : (is_string($first) ? $first : null);
                    }
                }
                $imageUrl = null;
                if (!empty($rawImage) && is_string($rawImage)) {
                    if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://') || str_starts_with($rawImage, '/')) {
                        $imageUrl = $rawImage;
                    } else {
                        $imageUrl = asset('/storage/' . ltrim($rawImage, '/'));
                    }
                }
                return [
                    'path' => $rawImage ?: null,
                    'url' => $imageUrl,
                ];
            })(),
            'quantity' => (int) $this->quantity,
            'unitPrice' => (float) $this->unit_price,
            'originalPrice' => $this->original_price ? (float) $this->original_price : null,
            'discountAmount' => (float) $this->discount_amount,
            'taxAmount' => (float) $this->tax_amount,
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
            'warrantyMonths' => (int) $this->warranty_months,
            'warrantyExpiresAt' => $this->warranty_expires_at?->toDateString(),
            'status' => $this->status,
            'isReturnable' => (bool) $this->is_returnable,
            'returnDay' => (int) $this->return_day,
            'note' => $this->note,

            // Timestamps
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
