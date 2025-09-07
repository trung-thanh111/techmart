<?php
namespace App\Repositories\Banner;

use App\Models\Banner;
use App\Repositories\BaseRepository;

class BannerRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Banner $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
