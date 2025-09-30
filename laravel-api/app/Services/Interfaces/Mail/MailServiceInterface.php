<?php

namespace App\Services\Interfaces\Mail;

interface MailServiceInterface
{
    /**
     * Gửi email xác thực đăng ký
     */
    public function sendEmailVerification(string $email, string $name, string $token): bool;
}
