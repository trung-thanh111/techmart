<?php

namespace App\Repositories\PasswordReset;

use App\Models\PasswordResetCode;
use App\Repositories\BaseRepository;

class PasswordResetRepository extends BaseRepository
{
    public function __construct(PasswordResetCode $model)
    {
        parent::__construct($model);
    }

    public function createCode(string $email, int $ttlMinutes = 10): PasswordResetCode
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($ttlMinutes);

        // cleanup previous codes for this email
        $this->model->byEmail($email)->delete();

        return $this->model->create([
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiresAt,
            'is_used' => false,
            'attempts' => 0,
        ]);
    }

    public function findValid(string $email, string $code): ?PasswordResetCode
    {
        return $this->model->byEmail($email)
            ->where('code', $code)
            ->valid()
            ->first();
    }

    public function deleteExpired(): int
    {
        return $this->model->where('expires_at', '<', now())->delete();
    }
}


