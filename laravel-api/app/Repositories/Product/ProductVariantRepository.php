<?php
namespace App\Repositories\Product;

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;

class ProductVariantRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        ProductVariant $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
