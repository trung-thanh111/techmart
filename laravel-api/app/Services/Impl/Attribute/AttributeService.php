<?php

namespace App\Services\Impl\Attribute;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Attribute\AttributeRepository;
use App\Http\Resources\Attribute\AttributeResource;
use App\Services\Interfaces\Attribute\AttributeServiceInterface;

class AttributeService extends BaseService implements AttributeServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['publish', 'attribute_catalogue_id'];

    protected $with = ['attribute_catalogues'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'attributes';
    protected $cacheTags = ['attributes:collection'];

    protected $useUpload = false;


    public function __construct(
        AttributeRepository $repository
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
        return new AttributeResource($resource);
    }
}
