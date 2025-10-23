<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Product\ProductCatalogueResource;
use App\Http\Resources\Product\ProductTagResource;
use App\Http\Resources\Product\ProductVariantResource;
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
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
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
            'productTags' => ProductTagResource::collection($this->whenLoaded('product_tags')),
            'productVariants' => ProductVariantResource::collection($this->whenLoaded('product_variants')),
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
        ];
    }
}
