<?php

namespace App\Services\Impl\System;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\System\SystemRepository;
use App\Http\Resources\System\SystemResource;
use App\Services\Interfaces\System\SystemServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;

class SystemService extends BaseService implements SystemServiceInterface
{
    protected $repository;

    protected $complexFilter = ['id'];
    protected $fieldSearchs = ['keyword'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'systems';
    protected $cacheTags = ['systems:collection'];

    protected $useUpload = false;

    public function __construct(
        SystemRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

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

    public function showByKeyword(string $keyword = '')
    {
        $model = $this->findByField('keyword', $keyword);
        if (!$model) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }
        return $model;
    }

    public function getStructure(): array
    {
        $groups = config('system.groups', []);
        $existingRecords = $this->repository->all()->keyBy('keyword');

        $result = [];
        foreach ($groups as $groupKey => $group) {
            $fields = [];
            foreach ($group['fields'] ?? [] as $fieldKey => $fieldConfig) {
                $fullKey = "{$groupKey}_{$fieldKey}";
                $record = $existingRecords->get($fullKey);
                $value = $record ? $record->content : ($fieldConfig['default'] ?? '');

                $fields[$fieldKey] = array_merge($fieldConfig, [
                    'keyword' => $fullKey,
                    'value' => $value,
                ]);
            }

            $result[$groupKey] = [
                'label' => $group['label'] ?? ucfirst($groupKey),
                'description' => $group['description'] ?? '',
                'fields' => $fields,
            ];
        }

        return $result;
    }

    public function saveSettings(array $settings, ?int $userId = null): bool
    {
        foreach ($settings as $key => $val) {
            $this->repository->updateOrCreate(
                ['keyword' => $key],
                [
                    'content' => is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) ($val ?? ''),
                    'publish' => 2,
                    'user_id' => $userId ?? 1,
                ]
            );
        }

        Cache::tags($this->cacheTags)->flush();
        return true;
    }

    public function getPublicConfigs(): array
    {
        return Cache::tags($this->cacheTags)->remember('systems:public_configs', 3600, function () {
            $published = $this->repository->all()->where('publish', 2);
            $keyMap = $published->pluck('content', 'keyword')->toArray();

            $groups = config('system.groups', []);
            $structured = [];

            foreach ($groups as $groupKey => $group) {
                $groupValues = [];
                foreach ($group['fields'] ?? [] as $fieldKey => $fieldConfig) {
                    $fullKey = "{$groupKey}_{$fieldKey}";
                    $val = array_key_exists($fullKey, $keyMap)
                        ? $keyMap[$fullKey]
                        : ($fieldConfig['default'] ?? '');
                    $groupValues[$fieldKey] = $val ?? '';
                }
                $structured[$groupKey] = $groupValues;
            }

            // Also provide a flat map for direct lookup by keyword
            $structured['map'] = $keyMap;

            return $structured;
        });
    }

    protected function getResource(Model $resource)
    {
        return new SystemResource($resource);
    }
}
