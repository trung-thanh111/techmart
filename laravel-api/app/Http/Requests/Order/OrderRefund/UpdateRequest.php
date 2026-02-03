<?php

namespace App\Http\Requests\Order\OrderRefund;

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
            "payment_id" => "{$sometime}|nullable|integer|exists:order_payments,id",
            "refund_number" => "{$sometime}|string|max:20",
            "amount" => "{$sometime}|numeric|min:0",
            "currency" => "{$sometime}|string|size:3",
            "reason" => "{$sometime}|string|in:customer_request,defective_product,wrong_product,not_as_described,damaged,other",
            "reason_description" => "{$sometime}|nullable|string",
            "status" => "{$sometime}|string|in:pending,approved,processing,completed,rejected",
            "approved_by" => "{$sometime}|nullable|integer|exists:users,id",
            "approved_at" => "{$sometime}|nullable|date",
            "refund_items" => "{$sometime}|nullable|array",
        ];
    }

    public function prepareForValidation() {}
}
