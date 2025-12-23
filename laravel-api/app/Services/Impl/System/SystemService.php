<?php

namespace App\Services\Impl\System;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\System\SystemRepository;
use App\Http\Resources\System\SystemResource;
use App\Services\Interfaces\System\SystemServiceInterface;

class SystemService extends BaseService implements SystemServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $fieldSearchs = ['keyword'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'systems';
    protected $cacheTags = ['systems:collection'];

    protected $useUpload = false;

    public function __construct(
        SystemRepository $repository
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
        return new SystemResource($resource);
    }
}
