<?php

namespace App\Http\Controllers\Api\V1\EmailVerification;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ApiResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mail\VerifyEmailRequest;
use App\Http\Requests\Mail\ResendEmailVerificationRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Interfaces\EmailVerification\EmailVerificationServiceInterface;

class EmailVerificationController extends Controller
{
    private $emailVerificationService;

    public function __construct(EmailVerificationServiceInterface $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Xác thực email với token
     */
    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        try {
            $result = $this->emailVerificationService->verifyEmail(
                $request->input('email'),
                $request->input('token')
            );

            if ($result['success']) {
                return ApiResource::ok($result['data'], $result['message']);
            } else {
                return ApiResource::message($result['message'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Gửi lại email xác thực
     */
    public function resend(ResendEmailVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->emailVerificationService->resendVerificationEmail(
                $request->input('email')
            );

            if ($result['success']) {
                return ApiResource::ok($result['data'], $result['message']);
            } else {
                return ApiResource::message($result['message'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Xác thực email từ link trong email (web route)
     */
    public function verifyFromEmail(Request $request, string $token): \Illuminate\View\View
    {
        try {
            // Lấy email từ signed URL
            $email = $request->get('email');

            if (!$email) {
                return view('mail.auth.verify-email', [
                    'error' => 'Email không hợp lệ',
                    'success' => false
                ]);
            }

            $result = $this->emailVerificationService->verifyEmail($email, $token);

            if ($result['success']) {
                return view('mail.auth.verify-email', [
                    'success' => true,
                    'message' => $result['message'],
                    'user' => $result['data']['user'] ?? null
                ]);
            } else {
                return view('mail.auth.verify-email', [
                    'error' => $result['message'],
                    'success' => false
                ]);
            }
        } catch (\Throwable $th) {
            return view('mail.auth.verify-email', [
                'error' => 'Có lỗi xảy ra khi xác thực email',
                'success' => false
            ]);
        }
    }

    /**
     * Kiểm tra trạng thái xác thực theo email
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $email = $request->input('email');
            $isVerified = \App\Models\User::where('email', $email)
                ->whereNotNull('email_verified_at')
                ->exists();

            return ApiResource::ok([
                'email' => $email,
                'verified' => $isVerified,
            ], $isVerified ? 'Email đã được xác thực' : 'Email chưa được xác thực');
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
