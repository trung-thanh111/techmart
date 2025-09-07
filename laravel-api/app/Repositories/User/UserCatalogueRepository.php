<?php
namespace App\Repositories\User;

use App\Models\UserCatalogue;
use App\Repositories\BaseRepository;

class UserCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        UserCatalogue $model
    ) {
        $this->model = $model;
        parent::__construct($model); // load model lên cho lớp cha
    }


}
