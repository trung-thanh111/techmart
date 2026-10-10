<?php

namespace App\Http\Controllers\Api\V1\System;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\System\System\BulkStoreRequest;
use App\Http\Requests\System\System\BulkUpdateRequest;
use App\Http\Requests\System\System\StoreRequest;
use App\Http\Requests\System\System\UpdateRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\System\SystemServiceInterface as SystemService;
use Illuminate\Support\Facades\Lang;

class SystemController extends BaseController
{
    protected $service;

    public function __construct(
        SystemService $service
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

    public function getByKeyword(string $keyword = ''): JsonResponse
    {
        return $this->showByKeyword($keyword);
    }

    public function structure(): JsonResponse
    {
        $structure = $this->service->getStructure();
        return ApiResource::ok($structure, Lang::get('message.successfully'));
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $userId = auth(\App\Enums\Common::ADMIN)->id() ?? 1;
        $this->service->saveSettings($validated['settings'], $userId);

        return ApiResource::message(Lang::get('message.save_data_successfully'));
    }

    public function publicConfigs(): JsonResponse
    {
        $configs = $this->service->getPublicConfigs();
        return ApiResource::ok($configs, Lang::get('message.successfully'));
    }

    protected function getService()
    {
        return $this->service;
    }
}
