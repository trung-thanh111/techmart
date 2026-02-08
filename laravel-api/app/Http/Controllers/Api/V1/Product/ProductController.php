<?php

namespace App\Http\Controllers\Api\V1\Product;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Product\Product\BulkStoreRequest;
use App\Http\Requests\Product\Product\BulkUpdateRequest;
use App\Http\Requests\Product\Product\StoreRequest;
use App\Http\Requests\Product\Product\UpdateRequest;
use App\Services\Interfaces\Product\ProductServiceInterface as ProductService;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\Lang;

class ProductController extends BaseController
{
    protected $service;

    public function __construct(
        ProductService $service
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

    public function updateView(int $id): JsonResponse
    {
        $payload = request()->only(['view_count']);
        if (!empty($payload)) {
            $this->service->update($id, $payload);
        }
        return ApiResource::ok(null, Lang::get('message.update_data_successfully'));
    }

    protected function getService()
    {
        return $this->service;
    }
}
