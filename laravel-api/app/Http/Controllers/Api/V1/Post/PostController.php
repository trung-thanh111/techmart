<?php

namespace App\Http\Controllers\Api\V1\Post;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Post\Post\BulkStoreRequest;
use App\Http\Requests\Post\Post\BulkUpdateRequest;
use App\Http\Requests\Post\Post\StoreRequest;
use App\Http\Requests\Post\Post\UpdateRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\Post\PostServiceInterface as PostService;
use Illuminate\Support\Facades\Lang;

class PostController extends BaseController
{
    protected $service;

    public function __construct(
        PostService $service
    ) {
        $this->service = $service;
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->baseSave($request);
    }

    public function bulkStore(BulkStoreRequest $request): JsonResponse
    {
        return $this->baseBulkSave($request, 'store');
    }

    public function bulkUpdate(BulkUpdateRequest $request): JsonResponse
    {
        return $this->baseBulkSave($request, 'update');
    }

    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        return $this->baseSave($request, $id);
    }

    public function attach(RelationRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRelation($request, $id, 'attach');
    }

    public function detach(RelationRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRelation($request, $id, 'detach');
    }

    public function restore(RestoreRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRestore($request, $id);
    }

    protected function getService()
    {
        return $this->service;
    }


    public function updateView(int $id): JsonResponse
    {
        $payload = request()->only(['view_count']);
        if (!empty($payload)) {
            $this->service->update($id, $payload);
        }
        return ApiResource::ok(null, Lang::get('message.update_data_successfully'));
    }
}
