<?php

namespace App\Services\Impl\Product;

use App\Enums\Common;
use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Product\ProductRepository;
use App\Http\Resources\Product\ProductResource;
use App\Services\Interfaces\Product\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = [
        'product_catalogues',
        'brands',
        'product_variants',
        'product_tags',
        'relateds',
    ];
    protected $relationFilter = [
        'product_catalogues',
        'brands',
        'product_tags',
    ];

    protected $cacheKeyPrefix = 'products';
    protected $cacheTags = ['products:collection'];

    protected $useUpload = true;

    public function __construct(
        ProductRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
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

    // override lại xử lý thêm variant
    protected function handleRelation(Request $request): self
    {
        $relations = $this->repository->getRelationable();
        if (count($relations)) {
            foreach ($relations as $key => $relation) {
                if ($request->has($relation)) {
                    $this->model->{$relation}()->sync($request->{$relation});
                }
            }
        }
        // xử lý productvariant
        $this->handleProductVariant($request);
        return $this;
    }

    private function handleProductVariant(Request $request): self
    {
        if (!$request->has('product_variants') || !is_array($request->input('product_variants'))) return $this;
        $variants = [];
        foreach ($request->product_variants as $variantData) {
            $variant = $this->model->product_variants()->create([
                ...$variantData,
                'price' => string_to_price($variantData['price']),
                'user_id' => auth(Common::API)->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
                $variant->variant_attributes()->attach($variantData['attributes']);
            }
        }

        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new ProductResource($resource);
    }
}
