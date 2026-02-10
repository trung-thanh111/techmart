<?php

namespace App\Services\Payment\Gateways;

use App\Enums\PaymentEnum;
use App\Exceptions\Payment\PaymentGatewayException;
use App\Exceptions\Payment\PaymentSignatureException;
use Illuminate\Support\Facades\Log;

/**
 * MoMo Payment Gateway Implementation
 *
 * Implement MoMo API v2:
 * - Tạo thanh toán (captureWallet) — QR + deeplink + web redirect
 * - Truy vấn trạng thái giao dịch
 * - Hoàn tiền
 * - Xác thực chữ ký webhook (IPN)
 *
 * @see https://developers.momo.vn/v3/vi/docs/payment/api/wallet/onetime
 */
class MoMoGateway extends AbstractPaymentGateway
{
    public function getGatewayName(): string
    {
        return PaymentEnum::GATEWAY_MOMO;
    }

    // ============================================================
    // Tạo thanh toán
    // ============================================================

    /**
     * Tạo yêu cầu thanh toán MoMo
     *
     * Flow:
     * 1. Build request payload theo format MoMo
     * 2. Tạo signature HMAC-SHA256
     * 3. POST /v2/gateway/api/create
     * 4. Trả về payUrl, deeplink, qrCodeUrl cho FE
     *
     * @param array $params [order_id, amount, order_info, request_id, extra_data?, items?, user_info?]
     * @return array [pay_url, deeplink, qr_code_url, request_id, gateway_response]
     * @throws PaymentGatewayException
     */
    public function createPayment(array $params): array
    {
        $partnerCode = $this->config['partner_code'];
        $accessKey = $this->config['access_key'];
        $ipnUrl = $this->config['ipn_url'];
        $redirectUrl = $this->config['redirect_url'];
        $requestType = $this->config['request_type'] ?? 'captureWallet';

        $orderId = $params['order_id'];
        $requestId = $params['request_id'];
        $amount = (int) $params['amount']; // MoMo yêu cầu Long, không có thập phân
        $orderInfo = $params['order_info'] ?? "Thanh toán đơn hàng #{$orderId}";
        $extraData = $params['extra_data'] ?? ''; // base64 encoded

        // Tạo chuỗi rawSignature theo thứ tự MoMo quy định
        $rawSignature = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$ipnUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}"
            . "&redirectUrl={$redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = $this->buildSignature($rawSignature);

        // Build request body
        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
            'lang' => 'vi',
        ];

        // Thêm items nếu có (tùy chọn)
        if (!empty($params['items'])) {
            $payload['items'] = $params['items'];
        }

        // Thêm userInfo nếu có
        if (!empty($params['user_info'])) {
            $payload['userInfo'] = $params['user_info'];
        }

        // Thêm deliveryInfo nếu có
        if (!empty($params['delivery_info'])) {
            $payload['deliveryInfo'] = $params['delivery_info'];
        }

        // Gọi MoMo API
        $apiPath = $this->config['api_paths']['create'] ?? '/v2/gateway/api/create';
        $response = $this->makeRequest('POST', $apiPath, $payload);

        // Kiểm tra kết quả
        $resultCode = $response['resultCode'] ?? -1;

        if ($resultCode !== PaymentEnum::MOMO_SUCCESS) {
            $errorMessage = PaymentEnum::getMoMoResultMessage($resultCode);

            throw new PaymentGatewayException(
                $errorMessage,
                $this->getGatewayName(),
                $resultCode,
                ['response' => $response]
            );
        }

        return [
            'pay_url' => $response['payUrl'] ?? null,
            'deeplink' => $response['deeplink'] ?? null,
            'qr_code_url' => $response['qrCodeUrl'] ?? null,
            'request_id' => $requestId,
            'gateway_response' => $response,
        ];
    }

    // ============================================================
    // Truy vấn trạng thái
    // ============================================================

    /**
     * Kiểm tra trạng thái giao dịch trên MoMo
     *
     * @param string $orderId Mã đơn hàng đã gửi khi tạo payment
     * @return array [status, transaction_id, amount, result_code, message, gateway_response]
     * @throws PaymentGatewayException
     */
    public function queryTransaction(string $orderId): array
    {
        $partnerCode = $this->config['partner_code'];
        $accessKey = $this->config['access_key'];
        $requestId = $this->generateRequestId();

        // Signature cho query: accessKey + orderId + partnerCode + requestId
        $rawSignature = "accessKey={$accessKey}"
            . "&orderId={$orderId}"
            . "&partnerCode={$partnerCode}"
            . "&requestId={$requestId}";

        $signature = $this->buildSignature($rawSignature);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'signature' => $signature,
            'lang' => 'vi',
        ];

        $apiPath = $this->config['api_paths']['query'] ?? '/v2/gateway/api/query';
        $response = $this->makeRequest('POST', $apiPath, $payload);

        $resultCode = $response['resultCode'] ?? -1;

        return [
            'status' => $this->mapResultCodeToStatus($resultCode),
            'transaction_id' => (string) ($response['transId'] ?? ''),
            'amount' => $response['amount'] ?? 0,
            'result_code' => $resultCode,
            'message' => $response['message'] ?? PaymentEnum::getMoMoResultMessage($resultCode),
            'pay_type' => $response['payType'] ?? null,
            'gateway_response' => $response,
        ];
    }

    // ============================================================
    // Hoàn tiền
    // ============================================================

    /**
     * Hoàn tiền giao dịch MoMo
     *
     * @param array $params [order_id, request_id, amount, transaction_id, description]
     * @return array [status, transaction_id, result_code, message, gateway_response]
     * @throws PaymentGatewayException
     */
    public function refund(array $params): array
    {
        $partnerCode = $this->config['partner_code'];
        $accessKey = $this->config['access_key'];

        $orderId = $params['order_id'];
        $requestId = $params['request_id'];
        $amount = (int) $params['amount'];
        $transId = (int) $params['transaction_id']; // transId từ MoMo
        $description = $params['description'] ?? '';

        // Signature cho refund
        $rawSignature = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&description={$description}"
            . "&orderId={$orderId}"
            . "&partnerCode={$partnerCode}"
            . "&requestId={$requestId}"
            . "&transId={$transId}";

        $signature = $this->buildSignature($rawSignature);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'amount' => $amount,
            'transId' => $transId,
            'description' => $description,
            'signature' => $signature,
            'lang' => 'vi',
        ];

        $apiPath = $this->config['api_paths']['refund'] ?? '/v2/gateway/api/refund';
        $response = $this->makeRequest('POST', $apiPath, $payload);

        $resultCode = $response['resultCode'] ?? -1;

        if ($resultCode !== PaymentEnum::MOMO_SUCCESS) {
            throw new PaymentGatewayException(
                PaymentEnum::getMoMoResultMessage($resultCode),
                $this->getGatewayName(),
                $resultCode,
                ['response' => $response]
            );
        }

        return [
            'status' => 'refunded',
            'transaction_id' => (string) ($response['transId'] ?? ''),
            'result_code' => $resultCode,
            'message' => $response['message'] ?? 'Hoàn tiền thành công',
            'gateway_response' => $response,
        ];
    }

    // ============================================================
    // Webhook / IPN Verification
    // ============================================================

    /**
     * Xác thực chữ ký webhook (IPN) từ MoMo
     *
     * Format signature IPN:
     * accessKey=$accessKey&amount=$amount&extraData=$extraData
     * &message=$message&orderId=$orderId&orderInfo=$orderInfo
     * &orderType=$orderType&partnerCode=$partnerCode&payType=$payType
     * &requestId=$requestId&responseTime=$responseTime
     * &resultCode=$resultCode&transId=$transId
     *
     * @param array $payload Nội dung IPN nhận từ MoMo
     * @return bool
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        $accessKey = $this->config['access_key'];
        $receivedSignature = $payload['signature'] ?? '';

        if (empty($receivedSignature)) {
            return false;
        }

        // Build rawSignature theo thứ tự MoMo quy định cho IPN
        $rawSignature = "accessKey={$accessKey}"
            . "&amount=" . ($payload['amount'] ?? '')
            . "&extraData=" . ($payload['extraData'] ?? '')
            . "&message=" . ($payload['message'] ?? '')
            . "&orderId=" . ($payload['orderId'] ?? '')
            . "&orderInfo=" . ($payload['orderInfo'] ?? '')
            . "&orderType=" . ($payload['orderType'] ?? '')
            . "&partnerCode=" . ($payload['partnerCode'] ?? '')
            . "&payType=" . ($payload['payType'] ?? '')
            . "&requestId=" . ($payload['requestId'] ?? '')
            . "&responseTime=" . ($payload['responseTime'] ?? '')
            . "&resultCode=" . ($payload['resultCode'] ?? '')
            . "&transId=" . ($payload['transId'] ?? '');

        return $this->verifySignature($rawSignature, $receivedSignature);
    }

    /**
     * Normalize MoMo IPN payload về format chuẩn
     */
    public function parseWebhookPayload(array $payload): array
    {
        $resultCode = (int) ($payload['resultCode'] ?? -1);

        return [
            'gateway_order_id' => $payload['orderId'] ?? '',
            'gateway_transaction_id' => (string) ($payload['transId'] ?? ''),
            'result_code' => (string) $resultCode,
            'is_success' => PaymentEnum::isMoMoSuccess($resultCode),
            'amount' => (int) ($payload['amount'] ?? 0),
            'message' => PaymentEnum::getMoMoResultMessage($resultCode),
        ];
    }

    // ============================================================
    // Signature — HMAC SHA256
    // ============================================================

    /**
     * Tạo chữ ký HMAC-SHA256
     * MoMo sử dụng secretKey làm key cho HMAC
     *
     * @param string $rawData Chuỗi dữ liệu cần ký
     * @return string Hex-encoded signature
     */
    protected function buildSignature(string $rawData): string
    {
        $secretKey = $this->config['secret_key'];
        return hash_hmac('sha256', $rawData, $secretKey);
    }

    /**
     * So sánh chữ ký an toàn (timing-safe)
     * Sử dụng hash_equals để chống timing attack
     *
     * @param string $rawData Chuỗi dữ liệu gốc
     * @param string $signature Chữ ký cần verify
     * @return bool
     */
    protected function verifySignature(string $rawData, string $signature): bool
    {
        $expectedSignature = $this->buildSignature($rawData);
        return hash_equals($expectedSignature, $signature);
    }

    // ============================================================
    // Helpers
    // ============================================================

    /**
     * Map MoMo resultCode sang transaction status nội bộ
     */
    protected function mapResultCodeToStatus(int $resultCode): string
    {
        return match ($resultCode) {
            PaymentEnum::MOMO_SUCCESS => PaymentEnum::STATUS_COMPLETED,
            PaymentEnum::MOMO_PENDING => PaymentEnum::STATUS_PENDING,
            1005, 1006 => PaymentEnum::STATUS_EXPIRED,
            default => PaymentEnum::STATUS_FAILED,
        };
    }
}
