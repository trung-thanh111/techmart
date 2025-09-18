<?php
namespace App\Services\Impl\Faq;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Faq\FaqCatalogueRepository;
use App\Http\Resources\Faq\FaqCatalogueResource;
use App\Services\Impl\ImageService;
use App\Services\Interfaces\Faq\FaqCatalogueServiceInterface;

class FaqCatalogueService extends BaseService implements FaqCatalogueServiceInterface {

protected $repository;
private const PIPELINE = 'default';

protected $complexFilter = ['id'];

protected $with = ['faq_catalogues', 'faqs'];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'faq_catalogues';
protected $cacheTags = ['faq_catalogues:collection'];

protected $useUpload = true;
protected $imageService;

    public function __construct(
        FaqCatalogueRepository $repository,
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
        return new FaqCatalogueResource($resource);
    }

}
