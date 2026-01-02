<?php
namespace App\Repositories\Menu;

use App\Models\MenuItem;
use App\Repositories\BaseRepository;

class MenuItemRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        MenuItem $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
