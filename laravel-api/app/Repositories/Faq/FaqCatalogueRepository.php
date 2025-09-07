<?php
namespace App\Repositories\Faq;

use App\Models\FaqCatalogue;
use App\Repositories\BaseRepository;

class FaqCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        FaqCatalogue $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
