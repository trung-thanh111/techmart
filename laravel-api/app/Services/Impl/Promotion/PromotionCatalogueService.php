<?php
namespace App\Services\Impl\Promotion;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Promotion\PromotionCatalogueRepository;
use App\Http\Resources\Promotion\PromotionCatalogueResource;
use App\Services\Interfaces\ImageServiceInterface as ImageService;
use App\Services\Interfaces\Promotion\PromotionCatalogueServiceInterface;

class PromotionCatalogueService extends BaseService implements PromotionCatalogueServiceInterface {

protected $repository;
private const PIPELINE = 'default';

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'promotion_catalogues';
protected $cacheTags = ['promotion_catalogues:collection'];

protected $useUpload = false;
protected $imageService;

    public function __construct(
        PromotionCatalogueRepository $repository,
        ImageService $imageService
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
    }

    protected function getRepository(){
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self {

        return $this->initializeBasicModelData($request)
                    ->uploadImage($request, 'image', self::PIPELINE, 'image');
    }

    protected function initializeBasicModelData(Request $request): self {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource){
        return new PromotionCatalogueResource($resource);
    }

}
