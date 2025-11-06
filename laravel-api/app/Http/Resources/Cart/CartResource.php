<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'customerId' => $this->customer_id ? (int) $this->customer_id : null,
            'guestId' => $this->guest_id,
            'mergedId' => $this->merged_id ? (int) $this->merged_id : null,
            'couponCode' => $this->coupon_code,
            'meta' => $this->meta,
            'publish' => (int) $this->publish,
            'cartItems' => CartItemResource::collection($this->whenLoaded('cart_items')),
        ];
    }
}
