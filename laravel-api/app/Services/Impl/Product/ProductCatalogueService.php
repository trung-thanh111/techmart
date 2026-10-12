<?php

namespace App\Services\Impl\Product;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Http\Resources\Product\ProductCatalogueResource;
use App\Services\Interfaces\ImageServiceInterface as ImageService;
use App\Services\Interfaces\Product\ProductCatalogueServiceInterface;

class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{


    protected $repository;

    private const PIPELINE = 'default';
    protected $complexFilter = ['id'];

    protected $with = ['users', 'products', 'products.brands', 'products.product_variants'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'product_catalogues';
    protected $cacheTags = ['product_catalogues:collection'];

    protected $useNestedSet = true;
    protected $imageService;


    public function __construct(
        ProductCatalogueRepository $repository,
        ImageService $imageService
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
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

        return $this->initializeBasicModelData($request)
            ->uploadImage($request, 'image', self::PIPELINE, 'image');
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new ProductCatalogueResource($resource);
    }

    public function paginate(Request $request)
    {
        $request->merge(['simple' => true]);
        return parent::paginate($request);
    }
}
