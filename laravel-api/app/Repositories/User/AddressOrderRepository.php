<?php
namespace App\Repositories\User;

use App\Models\AddressOrder;
use App\Repositories\BaseRepository;

class AddressOrderRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        AddressOrder $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
