<?php

namespace App\Observers;

use App\Enums\OrderEnum;
use App\Models\Order;
use App\Services\Impl\Inventory\InventorySyncService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function __construct(
        protected InventorySyncService $inventorySyncService
    ) {}

    /**
     * Handle the Order "created" event.
     * Tự động trừ kho nếu đơn tạo ra đã ở trạng thái confirmed hoặc paid
     */
    public function created(Order $order): void
    {
        try {
            $isPaid = $order->payment_status === OrderEnum::PAYMENT_STATUS_PAID;
            $isConfirmed = in_array($order->status, [
                OrderEnum::STATUS_CONFIRMED,
                OrderEnum::STATUS_PROCESSING,
                OrderEnum::STATUS_SHIPPED,
                OrderEnum::STATUS_DELIVERED,
            ]);

            if (($isPaid || $isConfirmed) && !$order->is_stock_deducted) {
                $this->inventorySyncService->deductStockForOrder($order);
            }
        } catch (\Throwable $e) {
            Log::error("[OrderObserver] Failed to deduct stock on created: #{$order->id} - {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     * Hook tự động:
     * - Trừ tồn kho (inventories & product_variants.stock) khi đơn thanh toán/xác nhận
     * - Hoàn lại tồn kho khi đơn bị hủy (cancelled) hoặc hoàn tiền (refunded)
     */
    public function updated(Order $order): void
    {
        try {
            $isPaid = $order->payment_status === OrderEnum::PAYMENT_STATUS_PAID;
            $isConfirmed = in_array($order->status, [
                OrderEnum::STATUS_CONFIRMED,
                OrderEnum::STATUS_PROCESSING,
                OrderEnum::STATUS_SHIPPED,
                OrderEnum::STATUS_DELIVERED,
            ]);

            // Trừ kho khi xác nhận hoặc thanh toán (chưa từng trừ)
            if (($isPaid || $isConfirmed) && !$order->is_stock_deducted) {
                $this->inventorySyncService->deductStockForOrder($order);
                return;
            }

            // Hoàn lại kho khi hủy hoặc hoàn tiền (đã từng trừ)
            $isCancelledOrRefunded = in_array($order->status, [
                OrderEnum::STATUS_CANCELLED,
                OrderEnum::STATUS_REFUNDED,
            ]);

            if ($isCancelledOrRefunded && $order->is_stock_deducted) {
                $this->inventorySyncService->releaseStockForOrder($order);
            }
        } catch (\Throwable $e) {
            Log::error("[OrderObserver] Failed to auto-sync inventory on updated: #{$order->id} - {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
