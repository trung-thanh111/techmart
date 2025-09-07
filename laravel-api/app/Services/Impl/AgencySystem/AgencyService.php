<?php

namespace App\Services\Impl\AgencySystem;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AgencySystem\AgencyRepository;
use App\Http\Resources\AgencySystem\AgencyResource;
use App\Services\Interfaces\AgencySystem\AgencyServiceInterface;

class AgencyService extends BaseService implements AgencyServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'agencies';
    protected $cacheTags = ['agencies:collection'];

    protected $useUpload = true;

    public function __construct(
        AgencyRepository $repository
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
        return new AgencyResource($resource);
    }
}
