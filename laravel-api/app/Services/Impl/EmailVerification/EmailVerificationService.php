<?php

namespace App\Services\Impl\EmailVerification;

use App\Models\User;
use App\Services\Interfaces\EmailVerification\EmailVerificationServiceInterface;
use App\Services\Interfaces\Mail\MailServiceInterface;
use App\Repositories\EmailVerification\EmailVerificationRepository;
use Illuminate\Support\Facades\Log;
use App\Traits\HasTransaction;

class EmailVerificationService implements EmailVerificationServiceInterface
{
    use HasTransaction;
    private $mailService;
    private $emailVerificationRepository;

    // State cho quy trình xác thực
    private ?string $currentEmail = null;
    private ?string $currentToken = null;
    private ?\App\Models\EmailVerification $emailVerificationModel = null;
    private ?User $user = null;

    public function __construct(
        MailServiceInterface $mailService,
        EmailVerificationRepository $emailVerificationRepository
    ) {
        $this->mailService = $mailService;
        $this->emailVerificationRepository = $emailVerificationRepository;
    }

    /**
     * Gửi email xác thực cho user mới đăng ký
     */
    public function sendVerificationEmail(string $email, string $name, array $registerPayload = []): array
    {
        try {
            // Cooldown/Reuse: nếu đã có token còn hạn, tái sử dụng để giảm spam
            $existing = $this->emailVerificationRepository->findReusableTokenByEmail($email);
            $emailVerification = $existing ?? $this->emailVerificationRepository->createVerificationToken($email, $registerPayload);

            // Gửi mail đồng bộ (không dùng queue)
            $sent = $this->mailService->sendEmailVerification($email, $name, $emailVerification->token);
            if (!$sent) {
                return [
                    'success' => false,
                    'message' => 'Không thể gửi email xác thực'
                ];
            }

            return [
                'success' => true,
                'message' => 'Email xác thực đã được gửi',
                'data' => [
                    'email' => $email,
                    'expires_at' => $emailVerification->expires_at
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Failed to send verification email to {$email}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi email xác thực'
            ];
        }
    }

    /**
     * Xác thực email với token
     */
    public function verifyEmail(string $email, string $token): array
    {
        try {
            return $this
                ->beginTransaction()
                ->validateToken($email, $token)
                ->resolveUser()
                ->markUserVerified()
                ->consumeToken()
                ->commit()
                ->buildVerifySuccessResponse();
        } catch (\Exception $e) {
            $this->rollback();
            Log::error("Failed to verify email {$email}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác thực email'
            ];
        }
    }

    private function validateToken(string $email, string $token): self
    {
        $this->currentEmail = $email;
        $this->currentToken = $token;
        $this->emailVerificationModel = $this->emailVerificationRepository->findValidToken($email, $token);
        if (!$this->emailVerificationModel) {
            throw new \InvalidArgumentException('Token không hợp lệ hoặc đã hết hạn');
        }
        return $this;
    }

    private function resolveUser(): self
    {
        $this->user = User::where('email', $this->currentEmail)->first();
        if (!$this->user) {
            $payload = $this->emailVerificationModel->payload ?? [];
            if (empty($payload)) {
                throw new \RuntimeException('Thiếu dữ liệu đăng ký');
            }
            $this->user = User::create([
                'name' => $payload['name'] ?? ($payload['first_name'] ?? null),
                'email' => $this->currentEmail,
                'password' => $payload['password'] ?? null,
            ]);
        }
        return $this;
    }

    private function markUserVerified(): self
    {
        if (is_null($this->user->email_verified_at)) {
            $this->user->update(['email_verified_at' => now()]);
        }
        return $this;
    }

    private function consumeToken(): self
    {
        $this->emailVerificationModel->markAsUsed();
        $this->emailVerificationRepository->deleteByEmail($this->currentEmail);
        return $this;
    }

    private function buildVerifySuccessResponse(): array
    {
        Log::info("Email verified successfully for: {$this->currentEmail}");
        return [
            'success' => true,
            'message' => 'Email đã được xác thực thành công',
            'data' => [
                'email' => $this->user->email,
                'verified' => true,
            ],
        ];
    }

    /**
     * Gửi lại email xác thực
     */
    public function resendVerificationEmail(string $email): array
    {
        try {
            // Flow hiện tại chưa tạo user trước verify: xử lý cả 2 trường hợp
            $user = User::where('email', $email)->first();
            if ($user) {
                if (!is_null($user->email_verified_at)) {
                    return [
                        'success' => false,
                        'message' => 'Tài khoản đã được xác thực'
                    ];
                }
                return $this->sendVerificationEmail($email, $user->name ?? $user->first_name);
            }

            // Chưa có user: lấy token pending và dùng payload để lấy tên
            $existing = $this->emailVerificationRepository->findReusableTokenByEmail($email);
            $name = $existing->payload['name'] ?? ($existing->payload['first_name'] ?? '');
            return $this->sendVerificationEmail($email, $name, $existing->payload ?? []);
        } catch (\Exception $e) {
            Log::error("Failed to resend verification email to {$email}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi lại email xác thực'
            ];
        }
    }

    /**
     * Xóa token hết hạn
     */
    public function cleanupExpiredTokens(): int
    {
        try {
            $deletedCount = $this->emailVerificationRepository->deleteExpiredTokens();
            Log::info("Cleaned up {$deletedCount} expired email verification tokens");
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("Failed to cleanup expired tokens: " . $e->getMessage());
            return 0;
        }
    }
}
