<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasQuery
{
    /**
     * scope trong tên các hàm sẽ được loại bỏ khi gọi
     * và chuyển về dạng camelCase đối với phần còn lại tên hàm
     * -> gọi vào phần đã chuyển về camelCase
     */

    // lọc theo quan hệ
    public function scopeWithRelation($query, array $with = [])
    {
        $query->with($with)->withCount($with);
        return $query;
    }

    //lọc theo từ khóa
    public function scopeKeyword($query, array $keyword = [])
    {
        if (!empty($keyword['q'])) {
            foreach ($keyword['fields'] as $field) {
                $query->orWhere($field, 'like', '%' . $keyword['q'] . '%');
            }
        }
        return $query;
    }

    // lọc theo dạng where bằng -> các trường kdl integer
    public function scopeSimple($query, array $filter = [])
    {
        if (count($filter)) {
            foreach ($filter as $key => $val) {
                if ($val !== null && $val !== '') {
                    $query->where($key, $val);
                }
            }
        }
        return $query;
    }

    // lọc nâng cao theo các toán tử khác !=, >, <, >=, <=, ...
    public function scopeComplex($query, array $filter = [])
    {
        if (count($filter)) {
            foreach ($filter as $field => $condition) {
                foreach ($condition as $operator => $val) {
                    switch ($operator) {
                        case 'gt': // lớn hơn (greater than)
                            $query->where($field, '>', $val);
                            break;
                        case 'gte': // lớn hơn hoặc bằng (greater than or equal)
                            $query->where($field, '>=', $val);
                            break;
                        case 'lt': // nhỏ hơn (less than)
                            $query->where($field, '<', $val);
                            break;
                        case 'lte': // nhỏ hơn hoặc bằng (less than or equal)
                            $query->where($field, '<=', $val);
                            break;
                        case 'eq': // bằng (equal)
                            $query->where($field, '=', $val);
                            break;
                        case 'neq': // không bằng (not equal)
                            $query->where($field, '!=', $val);
                            break;
                        case 'like': // giống như (like)
                            $query->where($field, 'like', '%' . $val . '%');
                            break;
                        case 'null':
                            $query->whereNull($field);
                            break;
                        case 'not_null':
                            $query->whereNotNull($field);
                            break;
                        case 'in': // nằm trong (in)
                            $in = explode(',', $val);
                            if (count($in)) {
                                $query->whereIn($field, $in);
                            }
                            break;
                        case 'between': // nằm giữa (between)
                            // dùng destructoring để lấy ra 2 giá trị min và max
                            [$min, $max] = explode(',', $val);
                            if ((int)$max > (int)$min) {
                                $query->whereBetween($field, [$min, $max]);
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $query;
    }

    public function scopeDate($query, array $filter = [])
    {
        if (count($filter)) {
            foreach ($filter as $field => $condition) {
                foreach ($condition as $operator => $date) {
                    $explodeDate = explode(',', $date);
                    if (count($explodeDate) === 1) {
                        $dateFormat = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                    }
                    switch ($operator) {
                        case 'gt':
                            $query->whereDate($field, '>', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'gte':
                            $query->whereDate($field, '>=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'lt':
                            $query->whereDate($field, '<', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'lte':
                            $query->whereDate($field, '<=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'eq':
                            $query->whereDate($field, '=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'neq':
                            $query->whereDate($field, '!=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'null':
                            $query->whereNull($field);
                            break;
                        case 'not_null':
                            $query->whereNotNull($field);
                            break;
                        case 'between':
                            [$startDate, $endDate] = explode(',', $date);
                            $startDate = Carbon::createFromFormat('d/m/Y', trim($startDate))->format('Y-m-d');
                            $endDate = Carbon::createFromFormat('d/m/Y', trim($endDate))->format('Y-m-d');

                            if ($endDate > $startDate) {
                                $query->whereBetween($field, [
                                    Carbon::parse($startDate)->startOfDay(),
                                    Carbon::parse($endDate)->endOfDay()
                                ]);
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $query;
    }

    private function applyRelationRecursive($query, array $conditions = [])
    {
        $fieldConditions = []; // chứa các field cần lọc và k phải là relation
        $relationConditions = []; // chứa các tên quan hệ cần lọc

        foreach ($conditions as $key => $val) {
            if (isset($val) && is_array($val) && array_intersect_key($val, array_flip(['gt', 'gte', 'lt', 'lte', 'in', 'eq', 'neq', 'between']))) {
                $fieldConditions[$key] = $val;
            } else {
                $relationConditions[$key] = $val;
            }
        }

        $this->applyFieldConditions($query, $fieldConditions);

        if (count($relationConditions)) {
            foreach ($relationConditions as $key => $val) {
                if (!empty($val)) {
                    $query->whereHas($key, function ($nestedQuery) use ($val) {
                        $this->applyRelationRecursive($nestedQuery, $val);
                    });
                }
            }
        }
    }

    private function applyFieldConditions($query, array $fieldConditions = [])
    {
        foreach ($fieldConditions as $field => $condition) {
            foreach ($condition as $operator => $valFilter) {
                switch ($operator) {
                    case 'gt':
                        $query->where($field, '>', $valFilter);
                        break;
                    case 'gte':
                        $query->where($field, '>=', $valFilter);
                        break;
                    case 'lt':
                        $query->where($field, '<', $valFilter);
                        break;
                    case 'lte':
                        $query->where($field, '<=', $valFilter);
                        break;
                    case 'eq':
                        $query->where($field, '=', $valFilter);
                        break;
                    case 'neq':
                        $query->where($field, '!=', $valFilter);
                        break;
                    case 'in':
                        $in = explode(',', $valFilter);
                        if (count($in)) {
                            $query->whereIn($field, $in);
                        }
                        break;
                    case 'between':
                        [$min, $max] = explode(',', $valFilter);
                        if ((int)$max > (int)$min) {
                            $query->whereBetween($field, [$min, $max]);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function scopeRelationFilter($query, array $relationFilter = [])
    {
        if (count($relationFilter)) {
            foreach ($relationFilter as $model => $conditions) {
                $query->whereHas($model, function ($query) use ($conditions) {
                    $this->applyRelationRecursive($query, $conditions);
                });
            }
        }

        return $query;
    }

    public function scopeCustomLimit($query, int $limit = 0)
    {
        if ($limit) {
            return $query->limit($limit);
        }
        return $query;
    }
}
