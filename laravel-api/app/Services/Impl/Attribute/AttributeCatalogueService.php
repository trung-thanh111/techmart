<?php
namespace App\Services\Impl\Attribute;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Attribute\AttributeCatalogueRepository;
use App\Http\Resources\Attribute\AttributeCatalogueResource;
use App\Services\Interfaces\Attribute\AttributeCatalogueServiceInterface;

class AttributeCatalogueService extends BaseService implements AttributeCatalogueServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = ['attributes'];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'attribute_catalogues';
protected $cacheTags = ['attribute_catalogues:collection'];

protected $useUpload = false;

    public function __construct(
        AttributeCatalogueRepository $repository
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
        return new AttributeCatalogueResource($resource);
    }

}
