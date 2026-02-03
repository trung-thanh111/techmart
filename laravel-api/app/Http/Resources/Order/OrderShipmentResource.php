<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderId' => $this->order_id,
            'trackingNumber' => $this->tracking_number,
            'carrier' => $this->carrier,
            'serviceType' => $this->service_type,
            'status' => $this->status,
            'weight' => $this->weight ? (float) $this->weight : null,
            'dimensions' => $this->dimensions,
            'packageCount' => (int) $this->package_count,
            'shippingCost' => (float) $this->shipping_cost,
            'estimatedDeliveryAt' => $this->estimated_delivery_at?->toISOString(),
            'pickedUpAt' => $this->picked_up_at?->toISOString(),
            'deliveredAt' => $this->delivered_at?->toISOString(),
            'receiverName' => $this->receiver_name,
            'deliveryNote' => $this->delivery_note,
            'trackingData' => $this->tracking_data,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
