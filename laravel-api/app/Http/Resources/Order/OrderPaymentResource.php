<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentResource extends JsonResource
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
            'transactionId' => $this->transaction_id,
            'paymentMethod' => $this->payment_method,
            'paymentGateway' => $this->payment_gateway,

            // Amount
            'amount' => (float) $this->amount,
            'currency' => $this->currency,

            // Status
            'status' => $this->status,

            // Card info
            'cardBrand' => $this->card_brand,
            'cardLast4' => $this->card_last4,

            // Response
            'gatewayResponse' => $this->gateway_response,
            'failureReason' => $this->failure_reason,

            // Timestamps
            'paidAt' => $this->paid_at?->toISOString(),
            'refundedAt' => $this->refunded_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
