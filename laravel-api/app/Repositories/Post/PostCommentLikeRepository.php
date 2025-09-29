<?php
namespace App\Repositories\Post;

use App\Models\PostCommentLike;
use App\Repositories\BaseRepository;

class PostCommentLikeRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        PostCommentLike $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
