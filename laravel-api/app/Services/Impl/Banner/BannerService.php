<?php

namespace App\Services\Impl\Banner;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Banner\BannerRepository;
use App\Http\Resources\Banner\BannerResource;
use App\Services\Interfaces\Banner\BannerServiceInterface;

class BannerService extends BaseService implements BannerServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['publish', 'banner_type'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'banners';
    protected $cacheTags = ['banners:collection'];

    protected $useUpload = true;

    public function __construct(
        BannerRepository $repository
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

    protected function getResource(Model $resource)
    {
        return new BannerResource($resource);
    }
}
