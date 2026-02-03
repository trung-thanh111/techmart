<?php

namespace App\Http\Requests\Order\OrderRefund;

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
            'payment_id' => 'nullable|integer|exists:order_payments,id',
            'refund_number' => 'required|string|max:20|unique:order_refunds,refund_number',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'reason' => 'required|string|in:customer_request,defective_product,wrong_product,not_as_described,damaged,other',
            'reason_description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,approved,processing,completed,rejected',
            'approved_by' => 'nullable|integer|exists:users,id',
            'approved_at' => 'nullable|date',
            'refund_items' => 'nullable|array',
        ];
    }

    public function prepareForValidation() {}
}
