<?php

namespace App\Services\Impl\Product;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Product\WishlistRepository;
use App\Http\Resources\Product\WishlistResource;
use App\Services\Interfaces\Product\WishlistServiceInterface;

class WishlistService extends BaseService implements WishlistServiceInterface
{
    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['product_variant_id'];

    protected $with = ['product_variants', 'product_variants.products'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'wishlists';
    protected $cacheTags = ['wishlists:collection'];

    protected $useUpload = false;

    public function __construct(
        WishlistRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * Override getResult để group theo productId trước khi transform
     */
    protected function getResult(): mixed
    {
        if ($this->result instanceof LengthAwarePaginator) {
            $this->result->getCollection()->load(['product_variants.products']);
            $groupedData = $this->groupWishlistsByProduct($this->result->items());
            return new LengthAwarePaginator(
                $groupedData,
                $this->result->total(),
                $this->result->perPage(),
                $this->result->currentPage(),
                ['path' => $this->result->path(), 'pageName' => 'page']
            );
        }

        return parent::getResult();
    }

    /**
     * Group wishlists theo productId
     *
     * @param array $wishlists - Mảng các wishlist models
     * @return array - Mảng đã được group và transform
     */
    protected function groupWishlistsByProduct($wishlists): array
    {
        return collect($wishlists)
            ->groupBy(function ($wishlist) {
                return $wishlist->product_variants?->product_id;
            })
            ->map(function ($items) {
                $firstItem = $items->first();
                $product = $firstItem->product_variants?->products;

                if (!$product) {
                    return null;
                }

                // lấy variants
                $variants = $items->map(function ($wishlist) {
                    $variant = $wishlist->product_variants;
                    return [
                        'id' => $variant?->id,
                        'name' => $variant?->name,
                        'price' => $variant?->price,
                        "attributeData" => $variant?->attribute_data,
                        'wishlistId' => $wishlist->id,
                    ];
                })->values()->toArray();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'priceOrigin' => (float) ($product->price_origin ?? 0),
                    'album' => $product->album,
                    'productCatalogueId' => (int) ($product->product_catalogue_id ?? 0),
                    'brandId' => (int) ($product->brand_id ?? 0),
                    'variant' => $variants,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    protected function prepareModelData(Request $request): self
    {
        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new WishlistResource($resource);
    }
}
