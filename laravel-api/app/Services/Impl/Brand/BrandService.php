<?php
namespace App\Services\Impl\Brand;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Brand\BrandRepository;
use App\Http\Resources\Brand\BrandResource;
use App\Services\Interfaces\Brand\BrandServiceInterface;

class BrandService extends BaseService implements BrandServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = ['users'];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'brands';
protected $cacheTags = ['brands:collection'];

protected $useUpload = true;

    public function __construct(
        BrandRepository $repository
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
        return new BrandResource($resource);
    }

}
