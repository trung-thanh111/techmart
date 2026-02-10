<?php

namespace App\Services\Impl\Payment;

use App\Enums\OrderEnum;
use App\Enums\PaymentEnum;
use App\Exceptions\Payment\PaymentException;
use App\Exceptions\Payment\PaymentSignatureException;
use App\Jobs\ProcessPaymentCallback;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Repositories\Payment\PaymentTransactionRepository;
use App\Services\Interfaces\Payment\PaymentServiceInterface;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Payment Service — Điều phối toàn bộ luồng thanh toán
 *
 * Responsibilities:
 * - Tạo payment request → gọi gateway → lưu transaction
 * - Xử lý webhook → verify signature → dispatch job
 * - Query trạng thái → gọi gateway query API
 * - Refund → gọi gateway refund API → update records
 */
class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        protected PaymentTransactionRepository $transactionRepo,
        protected PaymentGatewayFactory $gatewayFactory,
    ) {}

    // ============================================================
    // Tạo thanh toán
    // ============================================================

    /**
     * Tạo yêu cầu thanh toán cho đơn hàng
     *
     * Flow:
     * 1. Validate order (phải pending/confirmed, chưa paid)
     * 2. Check idempotency — nếu có pending transaction chưa hết hạn → trả lại payUrl cũ
     * 3. Tạo PaymentTransaction record (status=pending)
     * 4. Gọi gateway.createPayment()
     * 5. Cập nhật transaction với payUrl, gateway_response
     * 6. Update order.payment_status = 'pending'
     * 7. Return payUrl cho FE redirect
     */
    public function createPayment(int $orderId, string $gateway): array
    {
        return DB::transaction(function () use ($orderId, $gateway) {
            // 1. Tìm và validate order
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $this->validateOrderForPayment($order);

            // 2. Check idempotency — trả lại transaction cũ nếu còn hiệu lực
            $existingTransaction = $this->transactionRepo->findPendingByOrderId($orderId, $gateway);
            if ($existingTransaction && !$existingTransaction->isExpired()) {
                Log::channel($this->getLogChannel())->info(
                    "Idempotency hit: trả lại transaction pending #{$existingTransaction->id} cho order #{$orderId}"
                );

                return $this->formatPaymentResponse($existingTransaction);
            }

            // 3. Resolve gateway
            $gatewayInstance = $this->gatewayFactory->make($gateway);
            $requestId = $gatewayInstance->generateRequestId();

            // 4. Tạo transaction record (pending)
            $expiryMinutes = config('payment.transaction.expiry_minutes', 15);
            $amount = (int) $order->total_amount; // MoMo yêu cầu integer

            $transaction = PaymentTransaction::create([
                'order_id' => $orderId,
                'gateway' => $gateway,
                'transaction_type' => PaymentEnum::TYPE_PAYMENT,
                'request_id' => $requestId,
                'amount' => $amount,
                'currency' => $order->currency ?? 'VND',
                'status' => PaymentEnum::STATUS_PENDING,
                'expired_at' => now()->addMinutes($expiryMinutes),
            ]);

            try {
                // 5. Gọi gateway API
                $gatewayOrderId = $requestId; // Dùng requestId làm orderId gửi cho MoMo
                $result = $gatewayInstance->createPayment([
                    'order_id' => $gatewayOrderId,
                    'amount' => $amount,
                    'order_info' => "Thanh toán đơn hàng #{$order->code}",
                    'request_id' => $requestId,
                    'extra_data' => base64_encode(json_encode([
                        'internal_order_id' => $orderId,
                        'order_code' => $order->code,
                    ])),
                    'user_info' => [
                        'name' => $order->customer_name,
                        'phoneNumber' => $order->customer_phone,
                        'email' => $order->customer_email,
                    ],
                ]);

                // 6. Cập nhật transaction với response
                $transaction->update([
                    'pay_url' => $result['pay_url'] ?? null,
                    'deeplink' => $result['deeplink'] ?? null,
                    'qr_code_url' => $result['qr_code_url'] ?? null,
                    'gateway_request' => $this->redactForStorage([
                        'order_id' => $gatewayOrderId,
                        'amount' => $amount,
                        'request_id' => $requestId,
                    ]),
                    'gateway_response' => $result['gateway_response'] ?? null,
                ]);

                // 7. Update order payment_status
                $order->update([
                    'payment_status' => OrderEnum::PAYMENT_STATUS_PENDING,
                    'payment_gateway' => $gateway,
                ]);

                return $this->formatPaymentResponse($transaction);
            } catch (\Throwable $e) {
                // Gateway call thất bại → cập nhật transaction thành failed
                $transaction->update([
                    'status' => PaymentEnum::STATUS_FAILED,
                    'error_code' => 'GATEWAY_ERROR',
                    'error_message' => $e->getMessage(),
                ]);

                throw $e; // Re-throw để controller xử lý
            }
        });
    }

    // ============================================================
    // Webhook Handler
    // ============================================================

    /**
     * Xử lý webhook/IPN từ payment gateway
     *
     * Flow:
     * 1. Verify signature — reject nếu invalid
     * 2. Tìm transaction by orderId → idempotency check
     * 3. Update transaction status
     * 4. Dispatch async job để update Order + OrderPayment
     */
    public function handleWebhook(string $gateway, array $payload): bool
    {
        $logChannel = $this->getLogChannel();

        // 1. Verify signature
        $gatewayInstance = $this->gatewayFactory->make($gateway);

        if (!$gatewayInstance->verifyWebhookSignature($payload)) {
            Log::channel($logChannel)->warning(
                "[Webhook] Chữ ký KHÔNG hợp lệ từ {$gateway}"
            );

            throw new PaymentSignatureException($gateway);
        }

        // 2. Parse normalized data từ gateway
        $parsed = $gatewayInstance->parseWebhookPayload($payload);

        Log::channel($logChannel)->info(
            "[Webhook] Nhận IPN từ {$gateway}",
            ['gateway_order_id' => $parsed['gateway_order_id'], 'is_success' => $parsed['is_success']]
        );

        // 3. Tìm transaction bằng request_id (= gateway_order_id)
        $transaction = $this->transactionRepo->findByGatewayOrderId(
            $parsed['gateway_order_id'],
            $gateway
        );

        if (!$transaction) {
            Log::channel($logChannel)->error(
                "[Webhook] Không tìm thấy transaction cho gateway_order_id: {$parsed['gateway_order_id']}"
            );
            return false;
        }

        // 4. Idempotency: nếu đã xử lý rồi → skip
        if (in_array($transaction->status, [
            PaymentEnum::STATUS_COMPLETED,
            PaymentEnum::STATUS_REFUNDED,
        ])) {
            Log::channel($logChannel)->info(
                "[Webhook] Transaction #{$transaction->id} đã xử lý (status={$transaction->status}), skip"
            );
            return true;
        }

        // 5. Cập nhật transaction (gateway-agnostic)
        $newStatus = $parsed['is_success']
            ? PaymentEnum::STATUS_COMPLETED
            : PaymentEnum::STATUS_FAILED;

        $transaction->update([
            'status' => $newStatus,
            'gateway_transaction_id' => $parsed['gateway_transaction_id'],
            'gateway_response' => $payload,
            'error_code' => !$parsed['is_success'] ? $parsed['result_code'] : null,
            'error_message' => !$parsed['is_success'] ? $parsed['message'] : null,
            'paid_at' => $parsed['is_success'] ? now() : null,
        ]);

        // 6. Dispatch async job
        ProcessPaymentCallback::dispatch($transaction->id)->onQueue('payments');

        Log::channel($logChannel)->info(
            "[Webhook] Đã xử lý IPN cho transaction #{$transaction->id}, status={$newStatus}"
        );

        return true;
    }

    // ============================================================
    // Query Status
    // ============================================================

    /**
     * Truy vấn trạng thái thanh toán
     * Kết hợp dữ liệu local + query gateway real-time
     */
    public function queryPaymentStatus(int $orderId): array
    {
        $order = Order::findOrFail($orderId);
        $transactions = $this->transactionRepo->findByOrderId($orderId);

        // Lấy transaction mới nhất để query gateway
        $latestTransaction = $transactions->first();
        $gatewayStatus = null;

        if ($latestTransaction && $latestTransaction->status === PaymentEnum::STATUS_PENDING) {
            try {
                $gatewayInstance = $this->gatewayFactory->make($latestTransaction->gateway);
                $gatewayStatus = $gatewayInstance->queryTransaction($latestTransaction->request_id);

                // Sync lại status nếu gateway đã cập nhật
                if ($gatewayStatus['status'] !== $latestTransaction->status) {
                    $latestTransaction->update([
                        'status' => $gatewayStatus['status'],
                        'gateway_transaction_id' => $gatewayStatus['transaction_id'] ?? $latestTransaction->gateway_transaction_id,
                        'gateway_response' => $gatewayStatus['gateway_response'],
                        'paid_at' => $gatewayStatus['status'] === PaymentEnum::STATUS_COMPLETED ? now() : null,
                    ]);

                    // Nếu thành công → dispatch job
                    if ($gatewayStatus['status'] === PaymentEnum::STATUS_COMPLETED) {
                        ProcessPaymentCallback::dispatch($latestTransaction->id)->onQueue('payments');
                    }
                }
            } catch (\Throwable $e) {
                Log::channel($this->getLogChannel())->warning(
                    "Không thể query gateway cho transaction #{$latestTransaction->id}: {$e->getMessage()}"
                );
            }
        }

        return [
            'order_id' => $orderId,
            'order_code' => $order->code,
            'payment_status' => $order->payment_status,
            'transactions' => $transactions,
            'gateway_status' => $gatewayStatus,
        ];
    }

    // ============================================================
    // Refund
    // ============================================================

    /**
     * Hoàn tiền cho đơn hàng
     */
    public function refundPayment(int $orderId, float $amount, string $description): array
    {
        return DB::transaction(function () use ($orderId, $amount, $description) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Tìm transaction thanh toán thành công gần nhất
            $paymentTransaction = $this->transactionRepo->findCompletedByOrderId($orderId);

            if (!$paymentTransaction) {
                throw new PaymentException(
                    'Không tìm thấy giao dịch thanh toán thành công cho đơn hàng này',
                    'NO_COMPLETED_PAYMENT'
                );
            }

            // Validate số tiền refund
            if ($amount > $paymentTransaction->amount) {
                throw new PaymentException(
                    "Số tiền hoàn ({$amount}) không được lớn hơn số tiền đã thanh toán ({$paymentTransaction->amount})",
                    'INVALID_REFUND_AMOUNT'
                );
            }

            $gatewayInstance = $this->gatewayFactory->make($paymentTransaction->gateway);
            $requestId = $gatewayInstance->generateRequestId();

            // Tạo refund transaction record
            $refundTransaction = PaymentTransaction::create([
                'order_id' => $orderId,
                'order_payment_id' => $paymentTransaction->order_payment_id,
                'gateway' => $paymentTransaction->gateway,
                'transaction_type' => PaymentEnum::TYPE_REFUND,
                'request_id' => $requestId,
                'amount' => $amount,
                'currency' => $paymentTransaction->currency,
                'status' => PaymentEnum::STATUS_PROCESSING,
            ]);

            try {
                $result = $gatewayInstance->refund([
                    'order_id' => $paymentTransaction->request_id, // orderId gốc gửi cho MoMo
                    'request_id' => $requestId,
                    'amount' => (int) $amount,
                    'transaction_id' => $paymentTransaction->gateway_transaction_id,
                    'description' => $description,
                ]);

                $refundTransaction->update([
                    'status' => PaymentEnum::STATUS_REFUNDED,
                    'gateway_transaction_id' => $result['transaction_id'] ?? null,
                    'gateway_response' => $result['gateway_response'] ?? null,
                    'paid_at' => now(),
                ]);

                // Update order
                $order->update([
                    'payment_status' => OrderEnum::PAYMENT_STATUS_REFUNDED,
                    'refunded_at' => now(),
                ]);

                return [
                    'status' => 'refunded',
                    'refund_transaction_id' => $refundTransaction->id,
                    'amount' => $amount,
                    'message' => 'Hoàn tiền thành công',
                ];
            } catch (\Throwable $e) {
                $refundTransaction->update([
                    'status' => PaymentEnum::STATUS_FAILED,
                    'error_code' => 'REFUND_FAILED',
                    'error_message' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    // ============================================================
    // Helpers
    // ============================================================

    /**
     * Validate order có thể thanh toán không
     */
    protected function validateOrderForPayment(Order $order): void
    {
        // Chỉ cho phép thanh toán order pending hoặc confirmed
        $allowedStatuses = [OrderEnum::STATUS_PENDING, OrderEnum::STATUS_CONFIRMED];
        if (!in_array($order->status, $allowedStatuses)) {
            throw new PaymentException(
                "Đơn hàng #{$order->code} không ở trạng thái cho phép thanh toán (hiện tại: {$order->status})",
                'INVALID_ORDER_STATUS'
            );
        }

        // Không cho thanh toán order đã paid
        if ($order->payment_status === OrderEnum::PAYMENT_STATUS_PAID) {
            throw new PaymentException(
                "Đơn hàng #{$order->code} đã được thanh toán",
                'ALREADY_PAID'
            );
        }
    }

    /**
     * Format response trả về cho controller
     */
    protected function formatPaymentResponse(PaymentTransaction $transaction): array
    {
        return [
            'transaction_id' => $transaction->id,
            'request_id' => $transaction->request_id,
            'pay_url' => $transaction->pay_url,
            'deeplink' => $transaction->deeplink,
            'qr_code_url' => $transaction->qr_code_url,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'expired_at' => $transaction->expired_at?->toISOString(),
        ];
    }

    /**
     * Loại bỏ sensitive data trước khi lưu DB
     */
    protected function redactForStorage(array $data): array
    {
        unset($data['secret_key'], $data['access_key'], $data['signature']);
        return $data;
    }

    protected function getLogChannel(): string
    {
        return config('payment.logging.channel', 'stack');
    }
}
