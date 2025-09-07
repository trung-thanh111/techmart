<?php
namespace App\Services\Impl\Product;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Product\ProductVariantRepository;
use App\Http\Resources\Product\ProductVariantResource;
use App\Services\Interfaces\Product\ProductVariantServiceInterface;

class ProductVariantService extends BaseService implements ProductVariantServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [
    'products'
];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'product_variants';
protected $cacheTags = ['product_variants:collection'];

protected $useUpload = true;

    public function __construct(
        ProductVariantRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository(){
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self {

        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource){
        return new ProductVariantResource($resource);
    }

}
