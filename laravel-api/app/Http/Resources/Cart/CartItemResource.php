<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Load purchasable relation nếu chưa được load
        $purchasable = $this->purchasable;
        $product = null;
        $variant = null;

        if ($purchasable) {
            if ($this->purchasable_type === 'App\Models\ProductVariant') {
                $variant = $purchasable;
                // Load product relation nếu chưa có
                if (!$variant->relationLoaded('products')) {
                    $variant->load('products');
                }
                $product = $variant->products ?? null;
                // Đảm bảo product có album nếu chưa load
                if ($product && !$product->relationLoaded('album')) {
                    // Album thường là JSON column, không cần eager load
                }
            } elseif ($this->purchasable_type === 'App\Models\Product') {
                $product = $purchasable;
            }
        }

        return [
            'id' => $this->id,
            'cartId' => (int) $this->cart_id,
            'purchasableType' => $this->purchasable_type,
            'purchasableId' => (int) $this->purchasable_id,
            'quantity' => (int) $this->quantity,
            'price' => (float) $this->price,
            'meta' => $this->meta,
            'publish' => (int) $this->publish,
            'product' => $product ? new ProductResource($product->makeHidden(['productVariants'])) : null,
            'variant' => $variant ? new ProductVariantResource($variant) : null,
        ];
    }
}
