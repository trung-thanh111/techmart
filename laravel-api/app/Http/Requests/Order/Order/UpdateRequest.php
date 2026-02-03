<?php

namespace App\Http\Requests\Order\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $id = $this->route("order"); // Assuming route parameter is "order"
        $method = $this->getMethod();
        $sometime = ($method === "PATCH") ? "sometimes" : "";
        return [
            "code" => [
                $sometime,
                "string",
                "max:20",
                Rule::unique("orders")->ignore($this->route("order"))
            ],
            "user_id" => "nullable|integer|exists:users,id",
            "customer_email" => "{$sometime}|email|max:255",
            "customer_phone" => "nullable|string|max:20",
            "customer_name" => "{$sometime}|string|max:100",

            "subtotal" => "{$sometime}|numeric|min:0",
            "tax_amount" => "{$sometime}|numeric|min:0",
            "shipping_fee" => "{$sometime}|numeric|min:0",
            "discount_amount" => "nullable|numeric|min:0",
            "total_amount" => "{$sometime}|numeric|min:0",
            "currency" => "nullable|string|size:3",

            "status" => "{$sometime}|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded,failed",
            "payment_status" => "{$sometime}|string|in:unpaid,pending,paid,partially_paid,refunded,failed",
            "payment_method" => "nullable|string|max:50",
            "payment_gateway" => "nullable|string|max:50",
            "transaction_id" => "nullable|string|max:255",

            "shipping_method" => "nullable|string|max:50",
            "shipping_provider" => "nullable|string|max:50",
            "tracking_number" => "nullable|string|max:255",

            "shipping_address" => "{$sometime}|string|max:255",
            "shipping_city" => "nullable|string|max:100",
            "shipping_ward" => "nullable|string|max:100",
            "shipping_street" => "nullable|string|max:100",
            "shipping_country_code" => "nullable|string|size:2",
            "shipping_postal_code" => "nullable|string|max:20",

            "customer_note" => "nullable|string",
            "admin_note" => "nullable|string",

            "paid_at" => "nullable|date",

            "confirmed_at" => "nullable|date",
            "shipped_at" => "nullable|date",
            "delivered_at" => "nullable|date",
            "cancelled_at" => "nullable|date",
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
