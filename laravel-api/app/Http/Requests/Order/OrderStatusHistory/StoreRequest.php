<?php

namespace App\Http\Requests\Order\OrderStatusHistory;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'old_status' => 'nullable|string|max:50',
            'new_status' => 'required|string|max:50',
            'user_id' => 'nullable|integer|exists:users,id',
            'user_type' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];
    }

    public function prepareForValidation() {}
}
