<?php

namespace App\Services\Impl\Mail;

use App\Services\Interfaces\Mail\MailServiceInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class MailService implements MailServiceInterface
{
    public function sendEmailVerification(string $email, string $name, string $token): bool
    {
        $verificationUrl = $this->generateVerificationUrl($token, $email);
        return $this->send('mail.auth.verify-email', 'Xác nhận đăng ký tài khoản tại Techmart', $email, $name, [
            'name' => $name,
            'verificationUrl' => $verificationUrl,
            'token' => $token,
        ]);
    }

    /**
     * Tạo URL xác thực email
     */
    private function generateVerificationUrl(string $token, string $email): string
    {
        return URL::temporarySignedRoute(
            'email.verify',
            now()->addHour(),
            ['token' => $token, 'email' => $email]
        );
    }

    private function send(string $template, string $subject, string $toEmail, string $toName, array $data): bool
    {
        try {
            Mail::send($template, $data, function ($message) use ($toEmail, $toName, $subject) {
                $message->to($toEmail, $toName)->subject($subject);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('Mail send failed', [
                'template' => $template,
                'to' => $toEmail,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
