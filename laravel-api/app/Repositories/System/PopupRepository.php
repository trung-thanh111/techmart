<?php
namespace App\Repositories\System;

use App\Models\Popup;
use App\Repositories\BaseRepository;

class PopupRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Popup $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
