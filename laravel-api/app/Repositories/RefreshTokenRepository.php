<?php
namespace App\Repositories;

use App\Models\RefreshToken;
use App\Repositories\BaseRepository;

class RefreshTokenRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        RefreshToken $model
    ) {
        $this->model = $model;
        parent::__construct($model); // load lên cho lớp cha
    }

    public function findValidRefreshToken(string $refreshToken = ''): ?RefreshToken
    {
        return $this->model->where('refresh_token', $refreshToken)
                           ->whereDate('expires_at', '>', now())
                           ->where('is_revoked', false)
                           ->first();
    }

    public function revokeAllUserRefreshToken (int $used_id = 0) {
        return $this->model->where('user_id', $used_id)
                           ->where('is_revoked', false)
                           ->update(['is_revoked' => true]);
    }
}
