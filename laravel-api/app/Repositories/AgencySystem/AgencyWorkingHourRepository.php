<?php
namespace App\Repositories\AgencySystem;

use App\Models\AgencyWorkingHour;
use App\Repositories\BaseRepository;

class AgencyWorkingHourRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        AgencyWorkingHour $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
