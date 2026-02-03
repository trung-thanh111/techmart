<?php

namespace App\Http\Requests\Order\OrderPayment;

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
            'transaction_id' => 'required|string|unique:order_payments,transaction_id',
            'payment_method' => 'required|string|max:50',
            'payment_gateway' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'nullable|string|in:pending,processing,completed,failed,refunded,cancelled',
            'card_brand' => 'nullable|string|max:20',
            'card_last4' => 'nullable|string|size:4',
            'gateway_response' => 'nullable|array',
            'failure_reason' => 'nullable|string',
            'paid_at' => 'nullable|date',
            'refunded_at' => 'nullable|date',
        ];
    }

    public function prepareForValidation() {}
}
