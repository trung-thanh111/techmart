<?php
namespace App\Repositories\Attribute;

use App\Models\AttributeCatalogue;
use App\Repositories\BaseRepository;

class AttributeCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        AttributeCatalogue $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
