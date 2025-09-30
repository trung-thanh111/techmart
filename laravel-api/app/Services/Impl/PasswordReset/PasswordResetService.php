<?php

namespace App\Services\Impl\PasswordReset;

use App\Repositories\PasswordReset\PasswordResetRepository;
use App\Repositories\User\UserRepository;
use App\Services\Interfaces\PasswordReset\PasswordResetServiceInterface;
use App\Traits\HasTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class PasswordResetService implements PasswordResetServiceInterface
{
    use HasTransaction;

    public function __construct(
        private PasswordResetRepository $resetRepo,
        private UserRepository $userRepo,
    ) {
    }

    public function request(string $email): array
    {
        $user = $this->userRepo->findByField('email', $email);
        if (!$user) {
            return ['success' => false, 'message' => 'Email không tồn tại'];
        }

        $codeModel = $this->resetRepo->createCode($email, 10);

        $name = optional($this->userRepo->findByField('email', $email))?->name;
        Mail::to($email)->send(new ResetPasswordOtpMail($email, $codeModel->code, $name));

        return ['success' => true, 'message' => 'Đã gửi mã OTP tới email'];
    }

    public function resend(string $email): array
    {
        return $this->request($email);
    }

    public function verify(string $email, string $code): array
    {
        $model = $this->resetRepo->findValid($email, $code);
        if (!$model) {
            return ['success' => false, 'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn'];
        }
        return ['success' => true, 'message' => 'Mã OTP hợp lệ'];
    }

    public function reset(string $email, string $code, string $password): array
    {
        try {
            $this->beginTransaction();

            $model = $this->resetRepo->findValid($email, $code);
            if (!$model) {
                return ['success' => false, 'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn'];
            }

            $user = $this->userRepo->findByField('email', $email);
            if (!$user) {
                return ['success' => false, 'message' => 'Email không tồn tại'];
            }

            $user->password = Hash::make($password);
            $user->save();

            $model->markAsUsed();

            $this->commit();
            return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công'];
        } catch (\Throwable $th) {
            $this->rollback();
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }
}


