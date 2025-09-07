<?php
namespace App\Services\Impl\Post;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\TagRepository as PostTagRepository;
use App\Http\Resources\Post\TagResource;
use App\Services\Interfaces\Post\TagServiceInterface;

class TagService extends BaseService implements TagServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'post_tags';
protected $cacheTags = ['post_tags:collection'];

protected $useUpload = false;

    public function __construct(
        PostTagRepository $repository
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
        return new TagResource($resource);
    }

}
