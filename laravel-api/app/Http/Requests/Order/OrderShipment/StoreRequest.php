<?php

namespace App\Http\Requests\Order\OrderShipment;

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
            'tracking_number' => 'required|string|unique:order_shipments,tracking_number',
            'carrier' => 'required|string|max:100',
            'service_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'package_count' => 'nullable|integer|min:1',
            'shipping_cost' => 'nullable|numeric|min:0',
            'estimated_delivery_at' => 'nullable|date',
            'picked_up_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
            'receiver_name' => 'nullable|string|max:255',
            'delivery_note' => 'nullable|string',
            'tracking_data' => 'nullable|array',
        ];
    }

    public function prepareForValidation() {}
}
