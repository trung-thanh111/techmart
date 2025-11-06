<?php
namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\BaseRepository;

class CartRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Cart $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
