<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $otp,
        public ?string $name = null,
    ) {}

    public function build()
    {
        return $this
            ->subject('Mã OTP đặt lại mật khẩu')
            ->view('mail.auth.reset-password')
            ->with([
                'email' => $this->email,
                'otp' => $this->otp,
                'name' => $this->name,
            ]);
    }
}


