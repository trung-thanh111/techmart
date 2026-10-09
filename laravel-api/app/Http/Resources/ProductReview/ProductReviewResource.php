<?php

namespace App\Http\Resources\ProductReview;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'user_avatar' => $this->user_avatar ?: ($this->user?->image ? asset("/storage/{$this->user->image}") : "https://ui-avatars.com/api/?name=" . urlencode($this->user_name) . "&background=0284c7&color=fff"),
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'images' => $this->images ?: [],
            'publish' => (int) $this->publish,
            'likes_count' => (int) $this->likes_count,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
