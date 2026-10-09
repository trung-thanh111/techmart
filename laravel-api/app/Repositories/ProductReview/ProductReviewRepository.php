<?php

namespace App\Repositories\ProductReview;

use App\Models\ProductReview;
use App\Repositories\BaseRepository;

class ProductReviewRepository extends BaseRepository
{
    protected $model;

    public function __construct(ProductReview $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }
}
