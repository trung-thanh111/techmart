<?php
namespace App\Services\Impl\Faq;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Faq\FaqRepository;
use App\Http\Resources\Faq\FaqResource;
use App\Services\Interfaces\Faq\FaqServiceInterface;

class FaqService extends BaseService implements FaqServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'faqs';
protected $cacheTags = ['faqs:collection'];

protected $useUpload = false;

    public function __construct(
        FaqRepository $repository
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
        return new FaqResource($resource);
    }

}
