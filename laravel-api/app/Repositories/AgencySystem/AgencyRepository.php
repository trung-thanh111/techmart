<?php
namespace App\Repositories\AgencySystem;

use App\Models\Agency;
use App\Repositories\BaseRepository;

class AgencyRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Agency $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
