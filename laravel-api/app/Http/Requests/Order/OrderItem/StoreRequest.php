<?php

namespace App\Http\Requests\Order\OrderItem;

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
            'product_id' => 'required|integer|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'product_sku' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_attributes' => 'nullable|array',
            'product_image' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'warranty_months' => 'nullable|integer|min:0',
            'warranty_expires_at' => 'nullable|date',
            'status' => 'nullable|string|in:pending,processing,shipped,delivered,returned,cancelled',
            'is_returnable' => 'nullable|boolean',
            'return_day' => 'nullable|integer|min:0',
            'note' => 'nullable|string',
        ];
    }

    public function prepareForValidation() {}
}
