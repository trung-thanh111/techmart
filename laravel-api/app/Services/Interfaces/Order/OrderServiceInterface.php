<?php

namespace App\Services\Interfaces\Order;

use App\Models\Order;
use Illuminate\Http\Request;

interface OrderServiceInterface
{
    /**
     * Tạo đơn hàng mới với items, reserve stock, tạo history
     */
    public function createOrder(Request $request);

    /**
     * Update trạng thái đơn hàng với history tracking
     */
    public function updateStatus(int $orderId, string $newStatus, ?string $note = null): Order;

    /**
     * Hủy đơn hàng và release stock
     */
    public function cancelOrder(int $orderId, ?string $reason = null): Order;

    /**
     * Update trạng thái thanh toán
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): Order;
}
