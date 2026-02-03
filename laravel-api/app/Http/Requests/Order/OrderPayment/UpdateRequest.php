<?php

namespace App\Http\Requests\Order\OrderPayment;

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
            "transaction_id" => "{$sometime}|string",
            "payment_method" => "{$sometime}|string|max:50",
            "payment_gateway" => "{$sometime}|nullable|string|max:50",
            "amount" => "{$sometime}|numeric|min:0",
            "currency" => "{$sometime}|string|size:3",
            "status" => "{$sometime}|string|in:pending,processing,completed,failed,refunded,cancelled",
            "card_brand" => "{$sometime}|nullable|string|max:20",
            "card_last4" => "{$sometime}|nullable|string|size:4",
            "gateway_response" => "{$sometime}|nullable|array",
            "failure_reason" => "{$sometime}|nullable|string",
            "paid_at" => "{$sometime}|nullable|date",
            "refunded_at" => "{$sometime}|nullable|date",
        ];
    }

    public function prepareForValidation() {}
}
