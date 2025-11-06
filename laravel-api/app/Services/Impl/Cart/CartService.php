<?php

namespace App\Services\Impl\Cart;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Cart\CartRepository;
use App\Http\Resources\Cart\CartResource;
use App\Services\Interfaces\Cart\CartServiceInterface;

class CartService extends BaseService implements CartServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['guest_id'];

    protected $with = [
        'cart_items.purchasable',
        'cart_items.purchasable.products',
    ];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'carts';
    protected $cacheTags = ['carts:collection'];

    protected $useUpload = false;

    public function __construct(
        CartRepository $repository
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
        return new CartResource($resource);
    }
}
