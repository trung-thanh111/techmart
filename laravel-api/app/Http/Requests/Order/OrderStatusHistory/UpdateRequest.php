<?php

namespace App\Http\Requests\Order\OrderStatusHistory;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === "PATCH") ? "sometimes" : "";
        return [
            "order_id" => "{$sometime}|integer|exists:orders,id",
            "old_status" => "{$sometime}|nullable|string|max:50",
            "new_status" => "{$sometime}|string|max:50",
            "user_id" => "{$sometime}|nullable|integer|exists:users,id",
            "user_type" => "{$sometime}|nullable|string|max:50",
            "note" => "{$sometime}|nullable|string",
            "metadata" => "{$sometime}|nullable|array",
        ];
    }

    public function prepareForValidation() {}
}
