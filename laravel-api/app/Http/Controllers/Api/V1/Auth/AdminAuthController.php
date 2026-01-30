<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\Common;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ApiResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Interfaces\Auth\AuthServiceInterface as AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class AdminAuthController extends Controller
{
    private $authService;
    private $guard = Common::ADMIN;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->authenticate($request, $this->guard);
            return ApiResource::ok($response['data'], Lang::get('message.login_success'))->withCookie($response['authCookie']);
        } catch (ModelNotFoundException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AuthenticationException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $response = $this->authService->getMe($this->guard);
            return ApiResource::ok($response, Lang::get('message.get_user_success'));
        } catch (UserNotDefinedException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->refreshToken($request, $this->guard);
            return ApiResource::ok($response['data'], Lang::get('message.refresh_token_success'))->withCookie($response['authCookie']);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $cookie = $this->authService->logout($this->guard);
            return ApiResource::message(Lang::get('message.logout_success'))->withCookie($cookie);
        } catch (\Throwable $th) {
            return ApiResource::message($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
