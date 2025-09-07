<?php
namespace App\Services\Impl\User;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\AddressOrderRepository;
use App\Http\Resources\User\AddressOrderResource;
use App\Services\Interfaces\User\AddressOrderServiceInterface;

class AddressOrderService extends BaseService implements AddressOrderServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'address_orders';
protected $cacheTags = ['address_orders:collection'];

protected $useUpload = false;

    public function __construct(
        AddressOrderRepository $repository
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
        return new AddressOrderResource($resource);
    }

}
