<?php

namespace App\Services\Payment\Contracts;

/**
 * Interface cho tất cả Payment Gateway
 *
 * Mỗi gateway (MoMo, VNPay, Stripe...) phải implement interface này.
 * Đảm bảo consistency khi thêm gateway mới — chỉ cần tạo class mới
 * implement interface này và đăng ký trong PaymentGatewayFactory.
 */
interface PaymentGatewayInterface
{
    /**
     * Tạo yêu cầu thanh toán
     *
     * @param array $params [
     *   'order_id' => string,      // Mã đơn hàng
     *   'amount' => int,           // Số tiền (VND, không có thập phân)
     *   'order_info' => string,    // Mô tả đơn hàng
     *   'request_id' => string,    // Idempotency key
     *   'extra_data' => string,    // Dữ liệu thêm (base64 encoded)
     *   'items' => array,          // Danh sách sản phẩm (optional)
     *   'user_info' => array,      // Thông tin user (optional)
     *   'delivery_info' => array,  // Thông tin giao hàng (optional)
     * ]
     * @return array [
     *   'pay_url' => string,           // URL redirect user sang gateway
     *   'deeplink' => string|null,     // Deep link mobile
     *   'qr_code_url' => string|null,  // QR code
     *   'request_id' => string,        // Request ID đã gửi
     *   'gateway_response' => array,   // Raw response từ gateway
     * ]
     */
    public function createPayment(array $params): array;

    /**
     * Truy vấn trạng thái giao dịch
     *
     * @param string $orderId Mã đơn hàng đã gửi khi tạo payment
     * @return array [
     *   'status' => string,            // pending, completed, failed...
     *   'transaction_id' => string,    // Mã giao dịch gateway
     *   'amount' => int,
     *   'result_code' => int,
     *   'message' => string,
     *   'gateway_response' => array,
     * ]
     */
    public function queryTransaction(string $orderId): array;

    /**
     * Hoàn tiền giao dịch
     *
     * @param array $params [
     *   'order_id' => string,
     *   'request_id' => string,
     *   'amount' => int,
     *   'transaction_id' => int,   // Mã giao dịch gateway (transId)
     *   'description' => string,
     * ]
     * @return array [
     *   'status' => string,
     *   'transaction_id' => string,
     *   'result_code' => int,
     *   'message' => string,
     *   'gateway_response' => array,
     * ]
     */
    public function refund(array $params): array;

    /**
     * Xác thực chữ ký webhook/IPN từ gateway
     * Dùng để verify callback không bị giả mạo
     *
     * @param array $payload Nội dung webhook nhận được
     * @return bool True nếu signature hợp lệ
     */
    public function verifyWebhookSignature(array $payload): bool;

    /**
     * Parse webhook payload thành format chuẩn
     * Mỗi gateway có format IPN khác nhau → normalize về cấu trúc chung
     *
     * @param array $payload Raw webhook data
     * @return array [
     *   'gateway_order_id' => string,  // orderId gửi cho gateway lúc tạo payment
     *   'gateway_transaction_id' => string,  // transId từ gateway
     *   'result_code' => string,       // result code gốc từ gateway
     *   'is_success' => bool,
     *   'amount' => int,
     *   'message' => string,
     * ]
     */
    public function parseWebhookPayload(array $payload): array;

    /**
     * Trả về tên gateway
     * VD: 'momo', 'vnpay', 'stripe'
     */
    public function getGatewayName(): string;

    /**
     * Tạo unique request ID cho idempotency
     */
    public function generateRequestId(): string;
}
