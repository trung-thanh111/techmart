<?php
namespace App\Repositories\Brand;

use App\Models\Brand;
use App\Repositories\BaseRepository;

class BrandRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Brand $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
