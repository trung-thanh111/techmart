<?php
namespace App\Services\Impl\Product;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Product\WishlistRepository;
use App\Http\Resources\Product\WishlistResource;
use App\Services\Interfaces\Product\WishlistServiceInterface;

class WishlistService extends BaseService implements WishlistServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'wishlists';
protected $cacheTags = ['wishlists:collection'];

protected $useUpload = false;

    public function __construct(
        WishlistRepository $repository
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
        return new WishlistResource($resource);
    }

}
