<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderShipment;
use App\Models\OrderRefund;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderId = 6;
        $order = Order::find($orderId);

        if (!$order) {
            $this->command->error("Order with ID $orderId not found!");
            return;
        }

        $this->command->info("Seeding data for Order ID: $orderId ({$order->code})");

        DB::transaction(function () use ($orderId, $order) {
            // 1. Create Payment (Success)
            $payment = OrderPayment::create([
                'order_id' => $orderId,
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'payment_method' => 'momo',
                'payment_gateway' => 'MoMo Wallet',
                'amount' => $order->total_amount ?? 500000,
                'currency' => 'VND',
                'status' => 'completed',
                'card_brand' => null,
                'card_last4' => null,
                'gateway_response' => json_encode(['message' => 'Success', 'requestId' => uniqid()]),
                'paid_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]);
            $this->command->info("Created Order Payment: {$payment->id}");

            // 2. Create Shipment (In Transit)
            $shipment = OrderShipment::create([
                'order_id' => $orderId,
                'tracking_number' => 'GHN' . rand(100000, 999999) . 'VN',
                'carrier' => 'Giao Hàng Nhanh',
                'service_type' => 'express',
                'status' => 'in_transit',
                'weight' => 1.5,
                'package_count' => 1,
                'shipping_cost' => 35000,
                'estimated_delivery_at' => Carbon::now()->addDays(1),
                'picked_up_at' => Carbon::now()->subDay(),
                'tracking_data' => json_encode([
                    ['time' => Carbon::now()->subDay()->toDateTimeString(), 'status' => 'Đã lấy hàng'],
                    ['time' => Carbon::now()->subHours(12)->toDateTimeString(), 'status' => 'Đang luân chuyển'],
                ]),
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ]);
            $this->command->info("Created Order Shipment: {$shipment->id}");

            // 3. Create Refund (Pending - Partial)
            $refund = OrderRefund::create([
                'order_id' => $orderId,
                'payment_id' => $payment->id,
                'refund_number' => 'REF-' . strtoupper(uniqid()),
                'amount' => 100000, // Partial refund
                'currency' => 'VND',
                'reason' => 'defective_product',
                'reason_description' => 'Sản phẩm bị xước nhẹ ở góc',
                'status' => 'pending',
                'refund_items' => json_encode(['items' => ['product_id' => 1, 'quantity' => 1]]), // Dummy data
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ]);
            $this->command->info("Created Order Refund: {$refund->id}");
        });

        $this->command->info("Seeding completed successfully!");
    }
}
