<?php
namespace App\Repositories\Product;

use App\Models\ProductTag;
use App\Repositories\BaseRepository;

class ProductTagRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        ProductTag $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
