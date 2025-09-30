<?php

namespace App\Services\Interfaces\EmailVerification;

interface EmailVerificationServiceInterface
{
    /**
     * Gửi email xác thực cho user mới đăng ký
     */
    public function sendVerificationEmail(string $email, string $name): array;

    /**
     * Xác thực email với token
     */
    public function verifyEmail(string $email, string $token): array;

    /**
     * Gửi lại email xác thực
     */
    public function resendVerificationEmail(string $email): array;

    /**
     * Xóa token hết hạn
     */
    public function cleanupExpiredTokens(): int;
}
