<?php
namespace App\Services\Impl\Order;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Order\OrderRepository;
use App\Http\Resources\Order\OrderResource;
use App\Services\Interfaces\Order\OrderServiceInterface;

class OrderService extends BaseService implements OrderServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'orders';
protected $cacheTags = ['orders:collection'];

protected $useUpload = false;

    public function __construct(
        OrderRepository $repository
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
        return new OrderResource($resource);
    }

}
