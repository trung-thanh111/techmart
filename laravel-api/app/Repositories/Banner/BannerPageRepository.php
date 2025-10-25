<?php
namespace App\Repositories\Banner;

use App\Models\BannerPage;
use App\Repositories\BaseRepository;

class BannerPageRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        BannerPage $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
