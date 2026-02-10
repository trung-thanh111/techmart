<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho refund
 */
class RefundPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth + role check ở middleware
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount' => ['required', 'numeric', 'min:1000'], // MoMo yêu cầu tối thiểu 1000 VNĐ
            'description' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Mã đơn hàng là bắt buộc',
            'order_id.exists' => 'Đơn hàng không tồn tại',
            'amount.required' => 'Số tiền hoàn là bắt buộc',
            'amount.min' => 'Số tiền hoàn tối thiểu 1.000 VNĐ',
            'description.required' => 'Lý do hoàn tiền là bắt buộc',
        ];
    }
}
