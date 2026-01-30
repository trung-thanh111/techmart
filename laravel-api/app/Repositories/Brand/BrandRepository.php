<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use App\Repositories\BaseRepository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Brand $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }

    public function paginate(array $specs = []): Collection | LengthAwarePaginator | null
    {
        $this->model = $this->model->withCount('product_counts');

        return parent::paginate($specs);
    }
}
