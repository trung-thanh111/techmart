<?php

namespace App\Services\Interfaces\PasswordReset;

interface PasswordResetServiceInterface
{
    public function request(string $email): array;
    public function resend(string $email): array;
    public function verify(string $email, string $code): array;
    public function reset(string $email, string $code, string $password): array;
}


