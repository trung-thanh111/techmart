<?php

namespace App\Services\Impl\Menu;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Menu\MenuRepository;
use App\Http\Resources\Menu\MenuResource;
use App\Services\Interfaces\Menu\MenuServiceInterface;

class MenuService extends BaseService implements MenuServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = ['menu_items'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'menus';
    protected $cacheTags = ['menus:collection'];

    protected $useUpload = false;

    public function __construct(
        MenuRepository $repository
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
        return new MenuResource($resource);
    }
}
