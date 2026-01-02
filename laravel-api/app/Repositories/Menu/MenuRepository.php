<?php
namespace App\Repositories\Menu;

use App\Models\Menu;
use App\Repositories\BaseRepository;

class MenuRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Menu $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
