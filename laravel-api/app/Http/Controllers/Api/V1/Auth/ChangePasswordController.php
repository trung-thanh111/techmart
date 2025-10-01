<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\Auth\ChangePasswordServiceInterface;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ChangePasswordController extends Controller
{
    protected $changePasswordService;

    public function __construct(ChangePasswordServiceInterface $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
    }

    /**
     * Change user password
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = $this->checkUserLogin();
            if (!$user) {
                return ApiResource::error(Lang::get('message.user_not_logged_in'), 401);
            }

            $result = $this->changePasswordService->changePassword(
                $user->id,
                $request->validated()
            );

            if ($result['status']) {
                return ApiResource::ok($result, Lang::get('message.change_password_success'));
            }

            return ApiResource::message($result['message'], 400);
        } catch (\Exception $e) {
            return ApiResource::message(Lang::get('message.change_password_error'), 500);
        }
    }

    /**
     * Check if user is logged in
     *
     * @return \App\Models\User|null
     */
    private function checkUserLogin()
    {
        return auth(Common::API)->user();
    }
}
