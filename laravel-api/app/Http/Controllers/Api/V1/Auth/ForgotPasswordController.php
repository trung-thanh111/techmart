<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyForgotCodeRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\PasswordReset\PasswordResetServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{
    public function __construct(private PasswordResetServiceInterface $service) {}

    public function request(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $res = $this->service->request($request);
            if ($res['success']) {
                return ApiResource::ok([], $res['message']);
            }
            return ApiResource::message($res['message'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function resend(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->request($request);
    }

    public function verify(VerifyForgotCodeRequest $request): JsonResponse
    {
        try {
            $res = $this->service->verify($request);
            if ($res['success']) {
                return ApiResource::ok([], $res['message']);
            }
            return ApiResource::message($res['message'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $res = $this->service->reset($request);
            if ($res['success']) {
                return ApiResource::ok([], $res['message']);
            }
            return ApiResource::message($res['message'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
