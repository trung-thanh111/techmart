<?php

namespace App\Repositories\Cart;

use App\Models\CartItem;
use App\Repositories\BaseRepository;

class CartItemRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        CartItem $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }
}
