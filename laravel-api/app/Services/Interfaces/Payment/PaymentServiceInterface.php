<?php

namespace App\Services\Interfaces\Payment;

/**
 * Interface cho Payment Service
 * Orchestrate toàn bộ payment flow: tạo, callback, query, refund
 */
interface PaymentServiceInterface
{
    /**
     * Tạo yêu cầu thanh toán cho đơn hàng
     *
     * @param int $orderId ID đơn hàng cần thanh toán
     * @param string $gateway Tên gateway (momo, vnpay...)
     * @return array [pay_url, deeplink, qr_code_url, transaction_id]
     */
    public function createPayment(int $orderId, string $gateway): array;

    /**
     * Xử lý webhook/IPN từ payment gateway
     *
     * @param string $gateway Tên gateway gửi webhook
     * @param array $payload Nội dung webhook
     * @return bool True nếu xử lý thành công
     */
    public function handleWebhook(string $gateway, array $payload): bool;

    /**
     * Truy vấn trạng thái thanh toán của đơn hàng
     *
     * @param int $orderId ID đơn hàng
     * @return array [status, transactions, gateway_status]
     */
    public function queryPaymentStatus(int $orderId): array;

    /**
     * Hoàn tiền cho đơn hàng
     *
     * @param int $orderId ID đơn hàng
     * @param float $amount Số tiền hoàn
     * @param string $description Lý do hoàn tiền
     * @return array [status, refund_transaction_id]
     */
    public function refundPayment(int $orderId, float $amount, string $description): array;
}
