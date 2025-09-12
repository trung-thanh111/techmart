<?php

namespace App\Services\Impl\Post;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Post\PostCatalogueRepository;
use App\Http\Resources\Post\PostCatalogueResource;
use App\Services\Interfaces\Post\PostCatalogueServiceInterface;
use App\Services\Interfaces\ImageServiceInterface as ImageService;


class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{

    protected $repository;
    private const PIPELINE = 'default';

    protected $complexFilter = ['id'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'post_catalogues';
    protected $cacheTags = ['post_catalogues:collection'];
    protected $imageService;
    protected $useNestedSet = true;

    public function __construct(
        PostCatalogueRepository $repository,
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
        return new PostCatalogueResource($resource);
    }
}
