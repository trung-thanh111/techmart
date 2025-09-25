<?php

namespace App\Traits;

use App\Traits\HasUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HasTransaction
{

    use HasUpload;

    public function beginTransaction(): self
    {
        DB::beginTransaction();
        return $this;
    }

    public function commit(): self
    {
        DB::commit();
        return $this;
    }

    public function rollback()
    {
        DB::rollBack();
    }

    protected function beforeSave(Request $request, ?int $id = 0): self
    {

        if ($id) {
            $this->checkModelExists($id);
        }
        return $this;
    }

    protected function afterSave(Request $request): self
    {
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }
        if ($this->useUpload) {
            $this->moveImage($request, $this->cacheKeyPrefix);
        }
        // Chỉ clear cache cũ, chưa cache mới vì relation chưa được handle
        return $this->clearCacheById()->clearCacheByTags();
    }

    protected function handleRelation(Request $request): self
    {
        $relations = $this->repository->getRelationable();
        if (count($relations)) {
            foreach ($relations as $key => $relation) {
                if ($request->has($relation)) {
                    $this->model->{$relation}()->sync($request->{$relation});
                }
            }
        }
        return $this;
    }

    protected function afterHandleRelation(Request $request): self
    {
        // Cache lại data sau khi đã handle relation để đảm bảo data mới được cache
        return $this->cacheSingleRecord();
    }
    // các trường hợp trả data và format lại data theo resource mong muốn
    protected function getResult(): mixed
    {
        if ($this->result instanceof LengthAwarePaginator) {
            $this->result->through(function ($item) {
                if ($item instanceof Model) {
                    return $this->getResource($item);
                }
                return $item;
            });
        } else if ($this->result instanceof Collection) {
            $this->result->transform(function ($item) {
                if ($item instanceof Model) {
                    return $this->getResource($item);
                }
                return $item;
            });
        } else if ($this->result instanceof Model) {
            $this->result->load($this->with);
            return $this->getResource($this->result);
        }
        return $this->result;
    }

    // tiến hành thêm mới or update dựa trên id
    protected function saveModel(?int $id = null): self
    {
        $this->model = ($id)
            ? $this->repository->update($id, $this->modelData)
            : $this->repository->create($this->modelData);
        if (!$this->model) {
            throw new ModelNotFoundException(Lang::get('message.model_not_found'));
        }
        $this->result = $this->model;
        return $this;
    }

    protected function beforeDelete(int $id = 0): self
    {
        if (!$this->model = $this->repository->findById($id)) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }
        return $this;
    }

    protected function deleteModel(int $id = 0): self
    {
        $this->result = $this->repository->deleteByModel($this->model);
        return $this;
    }

    protected function afterDelete(int $id = 0): self
    {
        // kiểm tra xem các module service con có sử dụng nested hay không
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }
        $this->clearCacheById($id)->clearCacheByTags();
        return $this;
    }

    public function beforeBulkDelete(Request $request): self
    {
        $table = $this->repository->getTable();
        $this->checkModelsExists($request->ids, $table);
        return $this;
    }

    public function bulkModelDelete(Request $request): self
    {
        $this->result = $this->repository->bulkDelete($request->ids);
        return $this;
    }

    public function afterBulkDelete(Request $request): self
    {
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }
        foreach ($request->ids as $id) {
            $this->clearCacheById($id);
        }
        return $this->clearCacheByTags();
    }

    public function checkModelsExists(array $ids = [], string $table = ''): self
    {
        $models = DB::table($table)->whereIn('id', $ids)->get();
        if ($models->count() !== count($ids)) {
            throw new ModelNotFoundException(Lang::get('message.invalid_records'));
        }
        return $this;
    }

    protected function checkRelation(Request $request): self
    {
        $relationable = $this->repository->getRelationable();
        if (!in_array($request->with, $relationable)) {
            throw new NotFoundHttpException(Lang::get('message.relation_not_found'));
        }
        return $this;
    }

    public function beforeActionRelation(Request $request, int $id = 0): self
    {
        $this->checkModelExists($id)->checkRelation($request)->checkModelsExists($request->ids, $request->with);
        return $this;
    }

    public function relationAction(Request $request, string $action = ''): self
    {
        // gọi đến    tên quan hệ - action: attach, detach - data muốn gỡ quan hệ
        $this->result = $this->model->{$request->with}()->{$action}($request->ids);
        return $this;
    }

    public function afterActionRelation(): self
    {
        return $this->clearCacheByTags();
    }

    public function beforeBulkSave(Request $request, string $action = ''): self
    {
        if ($action === 'update') {
            $payload = $request->input('data', []);
            $ids = [];
            foreach ($payload as $key => $val) {
                $ids[] = $val['id']; // có được mảng id từ request
            }

            $this->model = $this->repository->findWhereIn($ids, 'id');
            if ($this->model->count() !== count($ids)) {
                throw new ModelNotFoundException(Lang::get('message.invalid_records'));
            }
        }
        return $this;
    }

    public function bulkModelSave(Request $request, string $action = ''): self
    {
        $fillable = $this->repository->getFillable(); // lấy ra các fill thuần trong bảng
        array_push($fillable, 'created_at, updated_at', 'id'); // push thêm vào 'created_at, updated_at'
        $payload = [];
        foreach ($request->data as $key => $value) {
            foreach ($value as $keyVal => $itemVal) {
                // nếu mà keyVal có trong fillable thì mới gán vào $payload
                if (in_array($keyVal, $fillable)) {
                    $payload[$key][$keyVal] = $itemVal;
                }
            }
        }
        // dùng match(php >= 8+) để thêm mới or update hợp lý
        $this->result = match ($action) {
            'store' => $this->repository->insert($payload),
            'update' => $this->repository->upsert($payload, ['id']),
        };
        return $this;
    }

    protected function afterBulkSave(Request $request, string $action = ''): self
    {
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }

        $virtualRequest = new Request();
        if ($action === 'store') {
            $countRecords = count($request->data);
            $requestData = [
                'type' => 'all',
                'sort' => 'id,desc',
                'limit' => $countRecords,
            ];
        } else if ($action === 'update') {
            $ids = array_column($request->data, 'id');
            $requestData = [
                'id' => [
                    'in' => implode(',', $ids),
                ]
            ];
        }
        $virtualRequest->merge($requestData);
        $records = $this->paginate($virtualRequest);
        foreach ($records as $key => $val) {
            $cacheKey = "{$this->cacheKeyPrefix}:single:{$val->id}";
            Cache::put($cacheKey, $val, $this->cacheTTL);
        }
        return $this->clearCacheByTags();
    }

    public function bulkHandleRelation(Request $request, string $action = ''): self
    {
        if ($action === 'store') {
            $this->model = $this->repository->findLimit(count($request->data));
        }

        $relationable = $this->repository->getRelationable();
        $payload = $request->data;
        krsort($payload); // sắp xếp giảm dần
        $payload = array_values($payload); // gán lại data mới cho payload
        if (count($relationable)) {
            foreach ($relationable as $key => $relation) {
                foreach ($this->model as $keyModel => $item) {
                    if (isset($payload[$keyModel][$relation]) && $payload[$keyModel][$relation]) {
                        $item->{$relation}()->sync($payload[$keyModel][$relation]);
                    }
                }
            }
        }
        return $this;
    }

    public function beforeRestore(Request $request, int $id = 0): self
    {

        return $this;
    }
    public function modelRestore(Request $request, int $id = 0): self
    {
        if ($id) {
            $this->result = $this->repository->restoreById($id);
        }
        return $this;
    }
    public function afterRestore(Request $request, int $id = 0): self
    {
        if ($this->useNestedSet) {
            $this->initializeNestedset();
        }
        return $this;
    }
}
