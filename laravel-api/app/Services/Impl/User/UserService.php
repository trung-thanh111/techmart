<?php

namespace App\Services\Impl\User;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\User\UserResource;
use App\Repositories\User\UserRepository;
use App\Services\Interfaces\ImageServiceInterface as ImageService;
use App\Services\Interfaces\User\UserServiceInterface;

class UserService extends BaseService implements UserServiceInterface
{

    private const PIPELINE = 'default';
    protected $repository;

    protected $simpleFilter = ['publish', 'user_catalogue_id'];
    protected $complexFilter = ['id'];
    protected $with = [];
    protected $relationFilter = ['user_catalogues'];

    protected $cacheKeyPrefix = 'users';
    protected $cacheTags = ['users:collection'];

    protected $imageService;
    protected $existingImages = [];

    public function __construct(
        UserRepository $repository,
        ImageService $imageService
    ) {
        $this->repository = $repository;
        $this->imageService = $imageService;
        parent::__construct();
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request, ?int $id = null): self
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
        return new UserResource($resource);
    }
}
