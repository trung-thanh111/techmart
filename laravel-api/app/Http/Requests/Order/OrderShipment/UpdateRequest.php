<?php

namespace App\Http\Requests\Order\OrderShipment;

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
            "tracking_number" => "{$sometime}|string",
            "carrier" => "{$sometime}|string|max:100",
            "service_type" => "{$sometime}|nullable|string|max:50",
            "status" => "{$sometime}|string|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned",
            "weight" => "{$sometime}|nullable|numeric|min:0",
            "dimensions" => "{$sometime}|nullable|array",
            "package_count" => "{$sometime}|integer|min:1",
            "shipping_cost" => "{$sometime}|numeric|min:0",
            "estimated_delivery_at" => "{$sometime}|nullable|date",
            "picked_up_at" => "{$sometime}|nullable|date",
            "delivered_at" => "{$sometime}|nullable|date",
            "receiver_name" => "{$sometime}|nullable|string|max:255",
            "delivery_note" => "{$sometime}|nullable|string",
            "tracking_data" => "{$sometime}|nullable|array",
        ];
    }

    public function prepareForValidation() {}
}
