<?php

namespace App\Repositories\EmailVerification;

use App\Models\EmailVerification;
use App\Repositories\BaseRepository;
use Illuminate\Support\Str;

class EmailVerificationRepository extends BaseRepository
{
    public function __construct(EmailVerification $model)
    {
        parent::__construct($model);
    }

    /**
     * Tạo token xác thực email mới
     */
    public function createVerificationToken(string $email, array $payload = []): EmailVerification
    {
        // Xóa các token cũ của email này
        $this->model->byEmail($email)->delete();

        // Tạo token mới
        $token = Str::random(64);
        $expiresAt = now()->addHour(); // Hết hạn sau 1 giờ

        return $this->model->create([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'is_used' => false,
            'payload' => $payload
        ]);
    }

    /**
     * Lấy token hợp lệ mới nhất để tái sử dụng (giảm tạo trùng lặp)
     */
    public function findReusableTokenByEmail(string $email): ?EmailVerification
    {
        return $this->model
            ->byEmail($email)
            ->valid()
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Tìm token hợp lệ theo email và token
     */
    public function findValidToken(string $email, string $token): ?EmailVerification
    {
        return $this->model
            ->byEmail($email)
            ->where('token', $token)
            ->valid()
            ->first();
    }

    /**
     * Xóa các token hết hạn
     */
    public function deleteExpiredTokens(): int
    {
        return $this->model
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Xóa token theo email
     */
    public function deleteByEmail(string $email): int
    {
        return $this->model->byEmail($email)->delete();
    }
}
