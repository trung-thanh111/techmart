<?php
namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Permission $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
