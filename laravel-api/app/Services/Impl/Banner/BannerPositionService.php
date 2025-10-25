<?php
namespace App\Services\Impl\Banner;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Banner\BannerPositionRepository;
use App\Http\Resources\Banner\BannerPositionResource;
use App\Services\Interfaces\Banner\BannerPositionServiceInterface;

class BannerPositionService extends BaseService implements BannerPositionServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'banner_positions';
protected $cacheTags = ['banner_positions:collection'];

protected $useUpload = false;

    public function __construct(
        BannerPositionRepository $repository
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
        return new BannerPositionResource($resource);
    }

}
