<?php
namespace App\Repositories\Attribute;

use App\Models\Attribute;
use App\Repositories\BaseRepository;

class AttributeRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Attribute $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
