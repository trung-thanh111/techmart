<?php
namespace App\Repositories;

use App\Models\CsrfToken;
use App\Repositories\BaseRepository;

class CsrfTokenRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        CsrfToken $model
    ) {
        $this->model = $model;
        parent::__construct($model); // load lên cho lớp cha
    }

    public function findByCsrfToken(string $csrfToken = ''): ?CsrfToken
    {
        return $this->model->where('csrf_token', $csrfToken)->first();
    }

    public function deleteCsrfTokenByUserid(int $userId = 0): bool{
        return $this->model->where('user_id', $userId)->delete();
    }
}
