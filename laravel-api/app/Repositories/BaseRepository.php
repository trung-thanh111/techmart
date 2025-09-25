<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class BaseRepository
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        $model
    ) {
        $this->model = $model;
    }

    public function paginate(array $specs = []): Collection | LengthAwarePaginator | null
    {
        return $this->model
            ->orderBy($specs['sort'][0], $specs['sort'][1]) // mảng sort thứ o là cột, thứ 1 là kiểu sort
            ->withRelation($specs['with']) // được định nghĩa trong hasQuery
            ->keyword($specs['keyword'])
            ->simple($specs['filters']['simple'])
            ->complex($specs['filters']['complex'])
            ->date($specs['filters']['date'])
            ->relationFilter($specs['filters']['relation'])
            ->customLimit($specs['limit'])
            ->when(
                $specs['type'],
                fn($q) => $q->get(),
                fn($q) => $q->paginate($specs['perpage'])
            );
    }

    public function create(array $payload = []): Model | null
    {
        return $this->model->create($payload)->refresh();
    }

    public function update(int $id = 0, array $payload = []): mixed
    {
        $model = $this->findById($id);
        $model->fill($payload);
        $model->save();
        return $model;
    }

    public function findById(int $id = 0, array $relation = [], array $column = ['*']): mixed
    {
        return $this->model->select($column)->with($relation)->find($id);
    }

    public function delete(int $id = 0): bool
    {
        $model = $this->model->findById($id);
        return $model->delete();
    }

    public function getFillable(): array
    {
        return $this->model->getFillable();
    }

    public function existsById(int $id = 0)
    {
        return $this->model->where('id', $id)->exists();
    }

    public function deleteByModel(Model $model)
    {
        return $model->delete();
    }

    public function findWhereIn(array $whereIn = [], string $field = '', string $sort = 'desc')
    {
        return $this->model->whereIn($field, $whereIn)->orderBy('id', $sort)->get();
    }

    public function bulkDelete(array $whereIn = [])
    {
        return $this->model->whereIn('id', $whereIn)->delete();
    }

    public function getRelationable(): array
    {
        return $this->model->relationable;
    }

    public function getTable()
    {
        return $this->model->getTable();
    }
    // thêm mới nhiểu bản ghi
    public function insert(array $payload = [])
    {
        return $this->model->insert($payload);
    }
    //cập nhật nhiều bản ghi
    public function upsert(array $payload = [], array $upsertkey = ['id'])
    {
        return $this->model->upsert($payload, $upsertkey);
    }

    public function findLimit($limit, $sort = 'desc')
    {
        return $this->model->orderBy('id', $sort)->limit($limit)->get();
    }

    public function findByField(string $field = '', mixed $value = null, array $relation = [], array $column = ['*'])
    {
        return $this->model->select($column)->with($relation)->where($field, $value)->first();
    }

    public function restoreById($id)
    {
        $data = $this->model->onlyTrashed()->find($id);
        if (!$data) {
            return null;
        }
        $data->restore();
        return $data;
    }
}
