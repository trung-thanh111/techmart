<?php
namespace App\Repositories\Promotion;

use App\Models\Promotion;
use App\Repositories\BaseRepository;

class PromotionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Promotion $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
