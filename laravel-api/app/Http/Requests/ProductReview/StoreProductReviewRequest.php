<?php

namespace App\Http\Requests\ProductReview;

use App\Http\Requests\BaseRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreProductReviewRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:2000',
            'user_name' => 'nullable|string|max:100',
            'user_email' => 'nullable|email|max:150',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string',
        ];
    }

    public function prepareForValidation()
    {
        $userId = null;
        $userName = $this->input('user_name');
        $userEmail = $this->input('user_email');
        $userAvatar = null;

        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $userId = $user->id;
                $userName = $userName ?: $user->name;
                $userEmail = $userEmail ?: $user->email;
                $userAvatar = $user->image ? asset("/storage/{$user->image}") : null;
            }
        } catch (\Throwable $e) {
            if (auth('api')->check()) {
                $user = auth('api')->user();
                $userId = $user->id;
                $userName = $userName ?: $user->name;
                $userEmail = $userEmail ?: $user->email;
                $userAvatar = $user->image ? asset("/storage/{$user->image}") : null;
            }
        }

        $this->merge([
            'user_id' => $userId,
            'user_name' => $userName ?: 'Khách hàng',
            'user_email' => $userEmail,
            'user_avatar' => $userAvatar,
            'publish' => 2, // Đánh giá tự động hiển thị ngay
        ]);
    }
}
