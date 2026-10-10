<?php

namespace App\Jobs;

use App\Enums\OrderEnum;
use App\Enums\PaymentEnum;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Job xử lý callback/webhook thanh toán bất đồng bộ
 *
 * Được dispatch sau khi PaymentService xác thực webhook thành công.
 * Responsibilities:
 * - Update Order payment_status (paid/failed)
 * - Auto-confirm order nếu thanh toán thành công
 *
 * payment_transactions là single source of truth cho payment data
 */
class ProcessPaymentCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public array $backoff = [5, 15, 30];

    public function __construct(
        protected int $transactionId
    ) {}

    public function handle(): void
    {
        $transaction = PaymentTransaction::find($this->transactionId);

        if (!$transaction) {
            Log::channel($this->getLogChannel())->error(
                "[ProcessPaymentCallback] Transaction #{$this->transactionId} không tồn tại"
            );
            return;
        }

        Log::channel($this->getLogChannel())->info(
            "[ProcessPaymentCallback] Xử lý transaction #{$transaction->id} "
                . "(order_id={$transaction->order_id}, status={$transaction->status})"
        );

        DB::transaction(function () use ($transaction) {
            $order = Order::lockForUpdate()->find($transaction->order_id);

            if (!$order) {
                Log::channel($this->getLogChannel())->error(
                    "[ProcessPaymentCallback] Order #{$transaction->order_id} không tồn tại"
                );
                return;
            }

            if ($transaction->status === PaymentEnum::STATUS_COMPLETED) {
                $this->handleSuccessfulPayment($transaction, $order);
            } elseif ($transaction->status === PaymentEnum::STATUS_FAILED) {
                $this->handleFailedPayment($transaction, $order);
            }
        });
    }

    protected function handleSuccessfulPayment(PaymentTransaction $transaction, Order $order): void
    {
        // 1. Update order payment status
        $order->update([
            'payment_status' => OrderEnum::PAYMENT_STATUS_PAID,
            'payment_gateway' => $transaction->gateway,
            'transaction_id' => $transaction->gateway_transaction_id,
            'paid_at' => $transaction->paid_at ?? now(),
        ]);

        // 2. Auto-confirm order nếu đang pending
        if ($order->status === OrderEnum::STATUS_PENDING) {
            $order->update([
                'status' => OrderEnum::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);

            Log::channel($this->getLogChannel())->info(
                "[ProcessPaymentCallback] Auto-confirmed order #{$order->id}"
            );
        }

        // 3. Tự động trừ tồn kho (inventories & product_variants.stock)
        app(\App\Services\Impl\Inventory\InventorySyncService::class)->deductStockForOrder($order);

        Log::channel($this->getLogChannel())->info(
            "[ProcessPaymentCallback] Thanh toán thành công — order #{$order->id}, "
                . "gateway_trans_id={$transaction->gateway_transaction_id}"
        );
    }

    protected function handleFailedPayment(PaymentTransaction $transaction, Order $order): void
    {
        $order->update([
            'payment_status' => OrderEnum::PAYMENT_STATUS_FAILED,
        ]);

        Log::channel($this->getLogChannel())->warning(
            "[ProcessPaymentCallback] Thanh toán thất bại — order #{$order->id}, "
                . "error: {$transaction->error_message}"
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel($this->getLogChannel())->error(
            "[ProcessPaymentCallback] Job FAILED cho transaction #{$this->transactionId}",
            ['error' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]
        );
    }

    protected function getLogChannel(): string
    {
        return config('payment.logging.channel', 'stack');
    }
}
