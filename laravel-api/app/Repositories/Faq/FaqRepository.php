<?php
namespace App\Repositories\Faq;

use App\Models\Faq;
use App\Repositories\BaseRepository;

class FaqRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Faq $model
    ) {
        $this->model = $model;
        parent::__construct($model);
    }


}
