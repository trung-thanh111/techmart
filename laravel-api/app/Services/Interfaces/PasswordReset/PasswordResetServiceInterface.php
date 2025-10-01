<?php

namespace App\Services\Interfaces\PasswordReset;

use Illuminate\Http\Request;

interface PasswordResetServiceInterface
{
    public function request(Request $request): array;
    public function resend(Request $request): array;
    public function verify(Request $request): array;
    public function reset(Request $request): array;
}


