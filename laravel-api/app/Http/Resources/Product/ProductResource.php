<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Faq\FaqResource;
use App\Http\Resources\Product\ProductCatalogueResource;
use App\Http\Resources\Product\ProductTagResource;
use App\Http\Resources\Product\ProductVariantResource;
use App\Http\Resources\Promotion\PromotionResource;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->boolean('simple', false)) {
            $firstVariant = $this->whenLoaded('product_variants', function () {
                $variant = $this->product_variants->first();
                return $variant ? [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'price' => $variant->price,
                    'attributeData' => $variant->attribute_data,
                ] : null;
            });

            return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'sku' => $this->sku,
                'priceOrigin' => $this->price_origin,
                'album' => $this->album,
                'season' => $this->season,
                'warranty' => $this->warranty,
                'sortOrder' => (int) $this->sort_order,
                'isHot' => (bool) $this->is_hot,
                'likeCount' => (int) $this->like_count,
                'productCatalogueId' => (int) $this->product_catalogue_id,
                'brandId' => (int) $this->brand_id,
                'publish' => (int) $this->publish,
                'variant' => $firstVariant,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'content' => $this->content,
            'priceOrigin' => $this->price_origin,
            'album' => $this->album,
            'season' => $this->season,
            'warranty' => $this->warranty,
            'sortOrder' => (int) $this->sort_order,
            'isHot' => (bool) $this->is_hot,
            'likeCount' => (int) $this->like_count,
            'productCatalogueId' => (int) $this->product_catalogue_id,
            'brandId' => (int) $this->brand_id,
            'faqId' => (int) $this->faq_id,
            'tagId' => (int) $this->tag_id,
            'publish' => (int) $this->publish,
            'productCatalogues' => ProductCatalogueResource::collection($this->whenLoaded('product_catalogues')),
            'brand' => $this->whenLoaded('brands', function () {
                return new BrandResource($this->brands);
            }),
            'relateds' => $this->whenLoaded('relateds', function () {
                return ProductResource::collection($this->relateds);
            }),
            'faqs' => FaqResource::collection($this->whenLoaded('product_faqs')),
            'productTags' => ProductTagResource::collection($this->whenLoaded('product_tags')),
            'productVariants' => ProductVariantResource::collection($this->whenLoaded('product_variants')),
            'promotions' => $this->resolvePromotions($request),
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
        ];
    }

    private function resolvePromotions(Request $request): array
    {
        $productId = $this->id;
        $variantIds = $this->relationLoaded('product_variants')
            ? $this->product_variants->pluck('id')->all()
            : [];
        $catalogueIds = $this->relationLoaded('product_catalogues')
            ? $this->product_catalogues->pluck('id')->all()
            : [];

        if ($this->product_catalogue_id) {
            $catalogueIds[] = (int) $this->product_catalogue_id;
        }

        $catalogueIds = array_values(array_unique(array_filter($catalogueIds)));

        if (!$productId && empty($variantIds) && empty($catalogueIds)) {
            return [
                'product' => [],
                'variants' => [],
                'catalogues' => [],
            ];
        }

        $now = now();

        $promotions = Promotion::query()
            ->with('promotion_targets')
            ->where('publish', 1)
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->whereHas('promotion_targets', function ($query) use ($productId, $variantIds, $catalogueIds) {
                $query->where(function ($targetQuery) use ($productId, $variantIds, $catalogueIds) {
                    $targetQuery->whereRaw('1 = 0');

                    if ($productId) {
                        $targetQuery->orWhere(function ($q) use ($productId) {
                            $q->where('target_type', 'product')
                                ->where('target_id', $productId);
                        });
                    }

                    if (!empty($variantIds)) {
                        $targetQuery->orWhere(function ($q) use ($variantIds) {
                            $q->where('target_type', 'product_variant')
                                ->whereIn('target_id', $variantIds);
                        });
                    }

                    if (!empty($catalogueIds)) {
                        $targetQuery->orWhere(function ($q) use ($catalogueIds) {
                            $q->where('target_type', 'product_catalogue')
                                ->whereIn('target_id', $catalogueIds);
                        });
                    }
                });
            })
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->get();

        $productPromotions = collect();
        $variantPromotions = [];
        $cataloguePromotions = [];

        foreach ($variantIds as $variantId) {
            $variantPromotions[$variantId] = collect();
        }

        foreach ($catalogueIds as $catalogueId) {
            $cataloguePromotions[$catalogueId] = collect();
        }

        foreach ($promotions as $promotion) {
            foreach ($promotion->promotion_targets as $target) {
                $targetType = $target->target_type ?? null;
                $targetId = isset($target->target_id) ? (int) $target->target_id : null;

                if ($targetType === 'product' && $targetId === (int) $productId) {
                    $productPromotions->put($promotion->id, $promotion);
                }

                if ($targetType === 'product_variant' && $targetId !== null && isset($variantPromotions[$targetId])) {
                    $variantPromotions[$targetId]->put($promotion->id, $promotion);
                }

                if ($targetType === 'product_catalogue' && $targetId !== null && isset($cataloguePromotions[$targetId])) {
                    $cataloguePromotions[$targetId]->put($promotion->id, $promotion);
                }
            }
        }

        return [
            'product' => PromotionResource::collection($productPromotions->values())->resolve($request),
            'variants' => collect($variantPromotions)
                ->filter(fn($collection) => $collection->isNotEmpty())
                ->map(fn($collection) => PromotionResource::collection($collection->values())->resolve($request))
                ->toArray(),
            'catalogues' => collect($cataloguePromotions)
                ->filter(fn($collection) => $collection->isNotEmpty())
                ->map(fn($collection) => PromotionResource::collection($collection->values())->resolve($request))
                ->toArray(),
        ];
    }
}
