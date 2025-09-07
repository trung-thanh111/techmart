<?php
namespace App\Repositories\Post;

use App\Models\PostTag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PostTag $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
