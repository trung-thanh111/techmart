<?php

namespace App\Services\Impl\System;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\System\PopupRepository;
use App\Http\Resources\System\PopupResource;
use App\Services\Impl\ImageService;
use App\Services\Interfaces\System\PopupServiceInterface;
use Illuminate\Support\Facades\DB;

class PopupService extends BaseService implements PopupServiceInterface
{

    private const PIPELINE = 'default';
    protected $repository;

    protected $complexFilter = ['id'];
    protected $fieldSearchs = ['title', 'slug', 'type'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'popups';
    protected $cacheTags = ['popups:collection'];

    protected $useUpload = true;
    protected $imageService;

    public function __construct(
        PopupRepository $repository,
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
        return new PopupResource($resource);
    }

    protected function afterSave(Request $request): self
    {
        $result = parent::afterSave($request);
        $isPriority = (bool)($request->input('priority') ?? false);
        if ($isPriority && $this->model) {
            $currentId = $this->model->id;
            $table = $this->repository->getTable();
            $affectedIds = DB::table($table)
                ->where('id', '!=', $currentId)
                ->where('priority', 1)
                ->pluck('id')
                ->all();

            if (!empty($affectedIds)) {
                DB::table($table)
                    ->whereIn('id', $affectedIds)
                    ->update(['priority' => 0]);

                foreach ($affectedIds as $affectedId) {
                    $this->clearCacheById($affectedId);
                }
            }
        }

        return $result;
    }
}
