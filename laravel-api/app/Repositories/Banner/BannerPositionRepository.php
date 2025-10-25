<?php
namespace App\Repositories\Banner;

use App\Models\BannerPosition;
use App\Repositories\BaseRepository;

class BannerPositionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        BannerPosition $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
