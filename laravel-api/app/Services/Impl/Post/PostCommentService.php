<?php

namespace App\Services\Impl\Post;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\PostCommentRepository;
use App\Http\Resources\Post\PostCommentResource;
use App\Services\Interfaces\Post\PostCommentServiceInterface;

class PostCommentService extends BaseService implements PostCommentServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = [];
    protected $relationFilter = [];
    protected $simpleFilter = ['publish', 'post_id'];

    protected $cacheKeyPrefix = 'post_comments';
    protected $cacheTags = ['post_comments:collection'];

    protected $useNestedSet = true;

    public function __construct(
        PostCommentRepository $repository
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
        return new PostCommentResource($resource);
    }

    // Override afterSave để xử lý nested set riêng cho comment
    protected function afterSave(Request $request, ?int $id = null): self
    {
        // Luôn rebuild toàn bộ để đảm bảo tính nhất quán
        // Vì comment tree thường không quá lớn, rebuild toàn bộ vẫn hiệu quả
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }
        
        return parent::afterSave($request, $id);
    }
}
