<?php
namespace App\Repositories\Inventory;

use App\Models\Inventory;
use App\Repositories\BaseRepository;

class InventoryRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Inventory $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
