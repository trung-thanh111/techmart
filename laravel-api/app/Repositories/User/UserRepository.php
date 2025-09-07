<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        User $model
    ) {
        $this->model = $model;
        parent::__construct($model); // load lên cho lớp cha
    }

}
