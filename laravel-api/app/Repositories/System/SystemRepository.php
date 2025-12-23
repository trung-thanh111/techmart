<?php
namespace App\Repositories\System;

use App\Models\System;
use App\Repositories\BaseRepository;

class SystemRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        System $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
