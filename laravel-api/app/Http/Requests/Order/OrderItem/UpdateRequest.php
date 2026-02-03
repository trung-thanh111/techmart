<?php

namespace App\Http\Requests\Order\OrderItem;

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
            "product_id" => "{$sometime}|integer|exists:products,id",
            "product_variant_id" => "{$sometime}|nullable|integer|exists:product_variants,id",
            "product_sku" => "{$sometime}|string|max:100",
            "product_name" => "{$sometime}|string|max:255",
            "product_description" => "{$sometime}|nullable|string",
            "product_attributes" => "{$sometime}|nullable|array",
            "product_image" => "{$sometime}|nullable|string|max:255",
            "quantity" => "{$sometime}|integer|min:1",
            "unit_price" => "{$sometime}|numeric|min:0",
            "original_price" => "{$sometime}|nullable|numeric|min:0",
            "discount_amount" => "{$sometime}|nullable|numeric|min:0",
            "tax_amount" => "{$sometime}|nullable|numeric|min:0",
            "subtotal" => "{$sometime}|numeric",
            "total" => "{$sometime}|numeric",
            "warranty_months" => "{$sometime}|nullable|integer|min:0",
            "warranty_expires_at" => "{$sometime}|nullable|date",
            "status" => "{$sometime}|string|in:pending,processing,shipped,delivered,returned,cancelled",
            "is_returnable" => "{$sometime}|boolean",
            "return_day" => "{$sometime}|nullable|integer|min:0",
            "note" => "{$sometime}|nullable|string",
        ];
    }

    public function prepareForValidation() {}
}
