<?php

namespace App\Services\Impl\Auth;

use App\Repositories\User\UserRepository;
use App\Services\Interfaces\Auth\ChangePasswordServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordService implements ChangePasswordServiceInterface
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Change user password
     */
    public function changePassword(int $userId, array $data): array
    {
        try {
            $user = $this->getUserById($userId);
            if (!$user) {
                return $this->errorResponse('Người dùng không tồn tại');
            }

            if (!$this->verifyOldPassword($data['password_old'], $user->password)) {
                return $this->errorResponse('Mật khẩu cũ không đúng');
            }

            if ($this->isSamePassword($data['password'], $user->password)) {
                return $this->errorResponse('Mật khẩu mới phải khác mật khẩu cũ');
            }

            $this->updateUserPassword($userId, $data['password']);

            return $this->successResponse('Đổi mật khẩu thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra khi đổi mật khẩu: ' . $e->getMessage());
        }
    }

    /**
     * Get user by ID
     */
    private function getUserById(int $userId)
    {
        return $this->userRepo->findById($userId);
    }

    /**
     * Verify old password
     */
    private function verifyOldPassword(string $passwordOld, string $hashedPassword): bool
    {
        return Hash::check($passwordOld, $hashedPassword);
    }

    /**
     * Check if new password is same as old password
     */
    private function isSamePassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    /**
     * Update user password
     */
    private function updateUserPassword(int $userId, string $password): void
    {
        $this->userRepo->update($userId, [
            'password' => Hash::make($password),
            'updated_at' => now(),
        ]);
    }
    /**
     * Success response
     */
    private function successResponse(string $message): array
    {
        return ['status' => true, 'message' => $message];
    }

    /**
     * Error response
     */
    private function errorResponse(string $message): array
    {
        return ['status' => false, 'message' => $message];
    }
}
