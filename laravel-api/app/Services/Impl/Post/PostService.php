<?php

namespace App\Services\Impl\Post;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\PostRepository;
use App\Http\Resources\Post\PostResource;
use App\Services\Impl\ImageService;
use App\Services\Interfaces\Post\PostServiceInterface;

class PostService extends BaseService implements PostServiceInterface
{

    protected $repository;
    private const PIPELINE = 'default';

    protected $simpleFilter = ['publish', 'post_catalogue_id', 'tag_id'];
    protected $complexFilter = ['id'];
    protected $sort = ['like_count', 'desc', 'id', 'desc'];

    protected $with = ['post_catalogues', 'post_tags', 'relateds'];
    protected $relationFilter = ['post_catalogues', 'post_tags'];

    protected $cacheKeyPrefix = 'posts';
    protected $cacheTags = ['posts:collection'];

    protected $imageService;


    public function __construct(
        PostRepository $repository,
        ImageService $imageService
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
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
        return $this->initializeBasicModelData($request)
            ->uploadImage($request, 'image', self::PIPELINE, 'image');
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new PostResource($resource);
    }

    public function paginate(Request $request)
    {
        $request->merge(['simple' => true]);
        return parent::paginate($request);
    }
}
