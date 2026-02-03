<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRefundResource extends JsonResource
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
            'paymentId' => $this->payment_id,
            'refundNumber' => $this->refund_number,

            // Amount
            'amount' => (float) $this->amount,
            'currency' => $this->currency,

            // Reason
            'reason' => $this->reason,
            'reasonDescription' => $this->reason_description,

            // Status
            'status' => $this->status,

            // Approval
            'approvedBy' => $this->approved_by,
            'approvedAt' => $this->approved_at?->toISOString(),

            // Items
            'refundItems' => $this->refund_items,

            // Timestamps
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
