<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'code' => $this->code,
            'userId' => $this->user_id,

            // Customer info
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ],

            // Pricing
            'subtotal' => (float) $this->subtotal,
            'taxAmount' => (float) $this->tax_amount,
            'shippingFee' => (float) $this->shipping_fee,
            'discountAmount' => (float) $this->discount_amount,
            'totalAmount' => (float) $this->total_amount,
            'currency' => $this->currency,

            // Status
            'status' => $this->status,
            'paymentStatus' => $this->payment_status,
            'paymentMethod' => $this->payment_method,
            'paymentGateway' => $this->payment_gateway,
            'transactionId' => $this->transaction_id,

            // Shipping method
            'shippingMethod' => $this->shipping_method,
            'shippingProvider' => $this->shipping_provider,
            'trackingNumber' => $this->tracking_number,

            // Shipping address
            'shippingAddress' => $this->shipping_address,
            'shippingCity' => $this->shipping_city,
            'shippingWard' => $this->shipping_ward,
            'shippingStreet' => $this->shipping_street,
            'shippingCountryCode' => $this->shipping_country_code,
            'shippingPostalCode' => $this->shipping_postal_code,

            // Notes
            'customerNote' => $this->customer_note,
            'adminNote' => $this->when((bool) (auth('admin')->user()?->is_admin ?? false), $this->admin_note),

            // Relations
            'items' => OrderItemResource::collection($this->whenLoaded('order_items')),
            'statusHistories' => OrderStatusHistoryResource::collection($this->whenLoaded('order_status_histories')),
            'payments' => OrderPaymentResource::collection($this->whenLoaded('payment_transactions')),
            'shipments' => OrderShipmentResource::collection($this->whenLoaded('order_shipments')),
            'refunds' => OrderRefundResource::collection($this->whenLoaded('order_refunds')),

            // Timestamps
            'confirmedAt' => $this->confirmed_at?->toISOString(),
            'paidAt' => $this->paid_at?->toISOString(),
            'shippedAt' => $this->shipped_at?->toISOString(),
            'deliveredAt' => $this->delivered_at?->toISOString(),
            'cancelledAt' => $this->cancelled_at?->toISOString(),
            'refundedAt' => $this->refunded_at?->toISOString(),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
