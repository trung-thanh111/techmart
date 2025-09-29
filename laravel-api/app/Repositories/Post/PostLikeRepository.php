<?php
namespace App\Repositories\Post;

use App\Models\PostLike;
use App\Repositories\BaseRepository;

class PostLikeRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PostLike $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
