<?php
namespace App\Services\Impl\Comment;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Comment\PostCommentRepository;
use App\Http\Resources\Comment\PostCommentResource;
use App\Services\Interfaces\Comment\PostCommentServiceInterface;

class PostCommentService extends BaseService implements PostCommentServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'post_comments';
protected $cacheTags = ['post_comments:collection'];

protected $useNestedSet = true;

    public function __construct(
        PostCommentRepository $repository
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
        return new PostCommentResource($resource);
    }

}
