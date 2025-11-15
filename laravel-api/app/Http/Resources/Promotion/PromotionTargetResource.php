 <?php

    namespace App\Http\Resources\Promotion;

    use App\Http\Resources\Product\ProductResource;
    use App\Http\Resources\Product\ProductVariantResource;
    use App\Http\Resources\Product\ProductCatalogueResource;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class PromotionTargetResource extends JsonResource
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
                'promotionId' => (int) $this->promotion_id,
                'productId' => $this->product_id ? (int) $this->product_id : null,
                'productVariantId' => $this->product_variant_id ? (int) $this->product_variant_id : null,
                'productCatalogueId' => $this->product_catalogue_id ? (int) $this->product_catalogue_id : null,
                'isGift' => (bool) $this->is_gift,
                'minQuantity' => (int) $this->min_quantity,
                'maxQuantity' => $this->max_quantity ? (int) $this->max_quantity : null,
                'product' => new ProductResource($this->whenLoaded('product')),
                'productVariant' => new ProductVariantResource($this->whenLoaded('product_variant')),
                'productCatalogue' => new ProductCatalogueResource($this->whenLoaded('product_catalogue')),
            ];
        }
    }
