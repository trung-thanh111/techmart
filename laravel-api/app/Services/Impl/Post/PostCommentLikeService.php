<?php

namespace App\Services\Impl\Post;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\PostCommentLikeRepository;
use App\Http\Resources\Post\PostCommentLikeResource;
use App\Services\Interfaces\Post\PostCommentLikeServiceInterface;

class PostCommentLikeService extends BaseService implements PostCommentLikeServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['comment_id', 'user_id'];
    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'post_comment_likes';
    protected $cacheTags = ['post_comment_likes:collection'];

    public function __construct(
        PostCommentLikeRepository $repository
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
        return new PostCommentLikeResource($resource);
    }
}
