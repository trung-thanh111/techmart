<?php

namespace App\Http\Resources\Promotion;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantResource;
use App\Http\Resources\Product\ProductCatalogueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'couponCode' => $this->coupon_code,
            'description' => $this->description,
            'promotionCatalogueId' => (int) $this->promotion_catalogue_id,
            'discountValue' => (float) $this->discount_value,
            'discountType' => $this->discount_type,
            'maxDiscountValue' => $this->max_discount_value ? (float) $this->max_discount_value : null,
            'startDate' => $this->start_date ? $this->start_date->format('Y-m-d H:i:s') : null,
            'endDate' => $this->end_date ? $this->end_date->format('Y-m-d H:i:s') : null,
            'priority' => (int) $this->priority,
            'canCombine' => (bool) $this->can_combine,
            'usedCount' => (int) $this->used_count,
            'publish' => (int) $this->publish,
            'promotionTargets' => PromotionTargetResource::collection($this->whenLoaded('promotion_targets')),
            'promotionCatalogue' => new PromotionCatalogueResource($this->whenLoaded('promotion_catalogues')),
        ];
    }
}
