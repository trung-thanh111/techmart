<?php
namespace App\Repositories\Post;

use App\Models\PostComment;
use App\Repositories\BaseRepository;

class PostCommentRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PostComment $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
