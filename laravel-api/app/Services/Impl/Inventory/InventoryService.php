<?php
namespace App\Services\Impl\Inventory;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Inventory\InventoryRepository;
use App\Http\Resources\Inventory\InventoryResource;
use App\Services\Interfaces\Inventory\InventoryServiceInterface;

class InventoryService extends BaseService implements InventoryServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'inventorys';
protected $cacheTags = ['inventorys:collection'];

protected $useUpload = false;

    public function __construct(
        InventoryRepository $repository
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
        return new InventoryResource($resource);
    }

}
