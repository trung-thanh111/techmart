<?php
namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\BaseRepository;

class PostRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Post $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
