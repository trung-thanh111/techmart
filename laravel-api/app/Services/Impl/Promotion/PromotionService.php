<?php
namespace App\Services\Impl\Promotion;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Promotion\PromotionRepository;
use App\Http\Resources\Promotion\PromotionResource;
use App\Services\Interfaces\Promotion\PromotionServiceInterface;

class PromotionService extends BaseService implements PromotionServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'promotions';
protected $cacheTags = ['promotions:collection'];

protected $useUpload = false;

    public function __construct(
        PromotionRepository $repository
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
        return new PromotionResource($resource);
    }

}
