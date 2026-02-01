<?php
namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Order $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
