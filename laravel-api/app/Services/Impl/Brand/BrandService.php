<?php

namespace App\Services\Impl\Brand;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Brand\BrandRepository;
use App\Http\Resources\Brand\BrandResource;
use App\Services\Interfaces\Brand\BrandServiceInterface;
use App\Services\Interfaces\ImageServiceInterface as ImageService;

class BrandService extends BaseService implements BrandServiceInterface
{

    private const PIPELINE = 'default';
    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = ['users'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'brands';
    protected $cacheTags = ['brands:collection'];
    protected $imageService;

    public function __construct(
        BrandRepository $repository,
        ImageService $imageService,
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
            ->uploadImage($request, 'image', self::PIPELINE, 'logo');
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new BrandResource($resource);
    }
}
