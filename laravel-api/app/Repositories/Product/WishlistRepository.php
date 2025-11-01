<?php
namespace App\Repositories\Product;

use App\Models\Wishlist;
use App\Repositories\BaseRepository;

class WishlistRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Wishlist $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
