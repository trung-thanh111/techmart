<?php
namespace App\Http\Controllers\Api\V1\Promotion;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Promotion\Promotion\BulkStoreRequest;
use App\Http\Requests\Promotion\Promotion\BulkUpdateRequest;
use App\Http\Requests\Promotion\Promotion\StoreRequest;
use App\Http\Requests\Promotion\Promotion\UpdateRequest;
use App\Services\Interfaces\Promotion\PromotionServiceInterface as PromotionService;

class PromotionController extends BaseController
{
    protected $service;

    public function __construct(
        PromotionService $service
    ) {
        $this->service = $service;
    }

    public function store(StoreRequest $request): JsonResponse{
        return $this->baseSave($request);
    }

    public function bulkStore(BulkStoreRequest $request): JsonResponse{
        return $this->baseBulkSave($request, 'store');

    }

    public function bulkUpdate(BulkUpdateRequest $request): JsonResponse{
        return $this->baseBulkSave($request, 'update');

    }

    public function update(UpdateRequest $request, int $id): JsonResponse{
        return $this->baseSave($request, $id);
    }

    public function attach(RelationRequest $request, int $id = 0): JsonResponse{
        return $this->baseRelation($request, $id, 'attach');
    }

    public function detach(RelationRequest $request, int $id = 0): JsonResponse{
        return $this->baseRelation($request, $id, 'detach');
    }

    public function restore(RestoreRequest $request, int $id = 0): JsonResponse{
        return $this->baseRestore($request, $id);
    }

    protected function getService(){
        return $this->service;
    }
}
