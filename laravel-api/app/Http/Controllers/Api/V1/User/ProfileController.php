<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Interfaces\User\UserServiceInterface as UserService;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\Lang;
use App\Http\Resources\User\UserResource;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(): JsonResponse
    {
        // Get currently authenticated user (api or admin)
        $user = auth('api')->user() ?: auth('admin')->user();

        if (!$user) {
            return ApiResource::error(Lang::get('message.user_not_found'), 401);
        }

        return ApiResource::ok(new UserResource($user), Lang::get('message.successfully'));
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?: auth('admin')->user();

        if (!$user) {
            return ApiResource::error(Lang::get('message.user_not_found'), 401);
        }

        // Remove sensitive fields from request so they aren't updated
        $request->request->remove('user_catalogue_id');
        $request->request->remove('email');
        $request->request->remove('password');

        // Use save method which handles upload and update
        $updatedUser = $this->userService->save($request, $user->id);

        return ApiResource::ok(new UserResource($updatedUser), Lang::get('message.update_data_successfully'));
    }
}
