<?php
namespace App\Services\Impl\Permission;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Permission\PermissionRepository;
use App\Http\Resources\Permission\PermissionResource;
use App\Services\Interfaces\Permission\PermissionServiceInterface;

class PermissionService extends BaseService implements PermissionServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'permissions';
protected $cacheTags = ['permissions:collection'];


    public function __construct(
        PermissionRepository $repository
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
        return new PermissionResource($resource);
    }

}
