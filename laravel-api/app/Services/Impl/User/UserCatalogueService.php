<?php
namespace App\Services\Impl\User;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\User\UserCatalogueResource;
use App\Repositories\User\UserCatalogueRepository;
use App\Services\Interfaces\User\UserCatalogueServiceInterface;

class UserCatalogueService extends BaseService implements UserCatalogueServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = ['users'];

protected $cacheKeyPrefix = 'user_catalogues';
protected $cacheTags = ['user_catalogues:collection'];


    public function __construct(
        UserCatalogueRepository $repository
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

    // protected function uploadImage(Request $request): self{
    //     return $this;
    // }

    protected function getResource(Model $resource){
        return new UserCatalogueResource($resource);
    }

}
