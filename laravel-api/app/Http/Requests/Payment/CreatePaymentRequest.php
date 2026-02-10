<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation cho tạo thanh toán
 */
class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth middleware đã kiểm tra
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'gateway' => [
                'required',
                'string',
                Rule::in(PaymentEnum::AVAILABLE_GATEWAYS),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Mã đơn hàng là bắt buộc',
            'order_id.exists' => 'Đơn hàng không tồn tại',
            'gateway.required' => 'Phương thức thanh toán là bắt buộc',
            'gateway.in' => 'Phương thức thanh toán không hợp lệ. Chấp nhận: ' . implode(', ', PaymentEnum::AVAILABLE_GATEWAYS),
        ];
    }
}
