<?php
namespace App\Repositories\Promotion;

use App\Models\PromotionCatalogue;
use App\Repositories\BaseRepository;

class PromotionCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PromotionCatalogue $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
