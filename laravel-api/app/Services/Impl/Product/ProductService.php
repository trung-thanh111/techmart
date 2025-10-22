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
        'product_variants',
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
        $variants = $request->input('product_variants');
        if ($request->has('product_variants')) {
            if ($this->model->exists) {
                $this->model->product_variants()->delete();
            }
            if (!is_array($variants) || empty($variants)) {
                return $this;
            }
        } else {
            return $this;
        }

        foreach ($variants as $variantData) {
            $priceRaw = $variantData['price'] ?? null;
            $price = is_null($priceRaw) ? null : (is_string($priceRaw) ? string_to_price($priceRaw) : (float)$priceRaw);
            $stockRaw = $variantData['stock'] ?? null;
            $stock = is_null($stockRaw) ? null : (int)$stockRaw;
            $publishRaw = $variantData['publish'] ?? 2;
            $publish = is_bool($publishRaw) ? ($publishRaw ? 1 : 0) : (int)$publishRaw;

            $attributeData = $variantData['attributeData'] ?? null;
            if (is_string($attributeData)) {
                $decoded = json_decode($attributeData, true);
                $attributeData = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($attributeData)) {
                $attributeData = [];
            }

            $variantAttributes = [
                'name' => $variantData['name'] ?? null,
                'code' => $variantData['code'] ?? null,
                'stock' => $stock,
                'sku' => $variantData['sku'] ?? null,
                'price' => $price,
                'url' => $variantData['url'] ?? null,
                'barcode' => $variantData['barcode'] ?? null,
                'attribute_data' => $attributeData,
                'publish' => $publish ?? 2,
                'created_at' => now(),
            ];

            $variant = $this->model->product_variants()->create($variantAttributes);

            // Attach attributes to pivot table
            if (isset($variantData['attributes']) && is_array($variantData['attributes']) && count($variantData['attributes'])) {
                $variant->variant_attributes()->attach($variantData['attributes']);
            }
        }

        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new ProductResource($resource);
    }

    public function paginate(Request $request)
    {
        //nếu là pthuc pagiante thì se trả về list simple nhanh gọn
        $request->merge(['simple' => true]);
        return parent::paginate($request);
    }
}
