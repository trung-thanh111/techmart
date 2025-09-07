<?php
namespace App\Repositories\Product;

use App\Models\ProductCatalogue;
use App\Repositories\BaseRepository;

class ProductCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        ProductCatalogue $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
