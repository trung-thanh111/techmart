<?php

namespace App\Services\Payment\Gateways;

use App\Enums\PaymentEnum;
use App\Exceptions\Payment\PaymentGatewayException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * VNPay Payment Gateway Implementation
 *
 * Khác biệt so với MoMo:
 * - Tạo payment: build URL query string + redirect (GET), không POST API
 * - Hash: HMAC-SHA512 (MoMo dùng SHA256)
 * - IPN: GET request với query params (MoMo POST JSON)
 * - Amount: nhân 100 trước khi gửi (VNPay quy định)
 * - Response code: "00" = success (string, không phải int 0)
 *
 * @see https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
 */
class VNPayGateway extends AbstractPaymentGateway
{
    public function getGatewayName(): string
    {
        return PaymentEnum::GATEWAY_VNPAY;
    }

    /**
     * Tạo URL thanh toán VNPay
     *
     * VNPay KHÔNG dùng POST API mà build query string → redirect user
     * Amount phải nhân 100 (VNPay quy định triệt tiêu thập phân)
     */
    public function createPayment(array $params): array
    {
        $tmnCode = $this->config['tmn_code'];
        $returnUrl = $this->config['return_url'];
        $ipnUrl = $this->config['ipn_url'];

        $amount = (int) $params['amount'];
        $orderId = $params['order_id'];
        $orderInfo = $this->sanitizeOrderInfo($params['order_info'] ?? "Thanh toan don hang {$orderId}");
        $requestId = $params['request_id'];
        $ipAddr = request()->ip() ?? '127.0.0.1';

        $vnNow = now('Asia/Ho_Chi_Minh');
        $createDate = $vnNow->format('YmdHis');
        $expireDate = $vnNow->copy()->addMinutes(
            config('payment.transaction.expiry_minutes', 15)
        )->format('YmdHis');

        // Build params — sắp xếp theo alphabet (VNPay yêu cầu)
        $vnpParams = [
            'vnp_Amount' => $amount * 100, // VNPay yêu cầu nhân 100
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode' => 'VND',
            'vnp_ExpireDate' => $expireDate,
            'vnp_IpAddr' => $ipAddr,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TmnCode' => $tmnCode,
            'vnp_TxnRef' => $requestId, // Dùng requestId làm TxnRef (unique)
            'vnp_Version' => '2.1.0',
        ];

        // Sort theo key alphabet
        ksort($vnpParams);

        // Build query string cho hash
        $hashData = http_build_query($vnpParams);
        $vnpSecureHash = $this->buildSignature($hashData);

        // Thêm hash vào params
        $vnpParams['vnp_SecureHash'] = $vnpSecureHash;

        // Build full payment URL
        $paymentUrl = $this->baseUrl
            . ($this->config['api_paths']['create'] ?? '/paymentv2/vpcpay.html')
            . '?' . http_build_query($vnpParams);

        $this->logRequest('GET', 'createPayment', ['vnp_TxnRef' => $requestId, 'vnp_Amount' => $amount * 100]);

        return [
            'pay_url' => $paymentUrl,
            'deeplink' => null, // VNPay không có deeplink
            'qr_code_url' => null, // QR tích hợp trong payment page
            'request_id' => $requestId,
            'gateway_response' => $vnpParams,
        ];
    }

    /**
     * Truy vấn trạng thái giao dịch VNPay
     *
     * POST https://sandbox.vnpayment.vn/merchant_webapi/api/transaction
     * vnp_Command = querydr
     */
    public function queryTransaction(string $orderId): array
    {
        $tmnCode = $this->config['tmn_code'];
        $requestId = $this->generateRequestId();
        $ipAddr = request()->ip() ?? '127.0.0.1';
        $vnNow = now('Asia/Ho_Chi_Minh');
        $createDate = $vnNow->format('YmdHis');
        $transactionDate = $vnNow->format('YmdHis');

        // Hash data cho querydr
        $hashData = $requestId . '|' . '2.1.0' . '|' . 'querydr' . '|'
            . $tmnCode . '|' . $orderId . '|' . $transactionDate . '|'
            . $createDate . '|' . $ipAddr . '|' . 'Truy van giao dich';

        $vnpSecureHash = $this->buildSignature($hashData);

        $payload = [
            'vnp_RequestId' => $requestId,
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'querydr',
            'vnp_TmnCode' => $tmnCode,
            'vnp_TxnRef' => $orderId,
            'vnp_OrderInfo' => 'Truy van giao dich',
            'vnp_TransactionNo' => 0,
            'vnp_TransactionDate' => $transactionDate,
            'vnp_CreateDate' => $createDate,
            'vnp_IpAddr' => $ipAddr,
            'vnp_SecureHash' => $vnpSecureHash,
        ];

        $apiUrl = $this->baseUrl . ($this->config['api_paths']['query'] ?? '/merchant_webapi/api/transaction');
        $response = $this->makeRequest('POST', $apiUrl, $payload);

        $responseCode = $response['vnp_ResponseCode'] ?? '99';
        $transactionStatus = $response['vnp_TransactionStatus'] ?? '99';

        return [
            'status' => $this->mapResponseCodeToStatus($transactionStatus),
            'transaction_id' => (string) ($response['vnp_TransactionNo'] ?? ''),
            'amount' => ($response['vnp_Amount'] ?? 0) / 100, // Chia 100 về VND
            'result_code' => $responseCode,
            'message' => $response['vnp_Message'] ?? PaymentEnum::getVNPayResultMessage($responseCode),
            'gateway_response' => $response,
        ];
    }

    /**
     * Hoàn tiền VNPay
     *
     * POST https://sandbox.vnpayment.vn/merchant_webapi/api/transaction
     * vnp_Command = refund
     */
    public function refund(array $params): array
    {
        $tmnCode = $this->config['tmn_code'];
        $requestId = $params['request_id'];
        $orderId = $params['order_id'];
        $amount = (int) $params['amount'];
        $transactionId = $params['transaction_id'];
        $description = $params['description'] ?? '';
        $ipAddr = request()->ip() ?? '127.0.0.1';
        $vnNow = now('Asia/Ho_Chi_Minh');
        $createDate = $vnNow->format('YmdHis');
        $transactionDate = $vnNow->format('YmdHis');

        // vnp_TransactionType: 02 = hoàn toàn phần, 03 = hoàn 1 phần
        $transactionType = '02';

        // Hash data cho refund
        $hashData = $requestId . '|' . '2.1.0' . '|' . 'refund' . '|'
            . $tmnCode . '|' . $transactionType . '|' . $orderId . '|'
            . ($amount * 100) . '|' . $transactionId . '|' . $transactionDate . '|'
            . Auth::user()?->email ?? 'admin' . '|' . $createDate . '|' . $ipAddr . '|'
            . $description;

        $vnpSecureHash = $this->buildSignature($hashData);

        $payload = [
            'vnp_RequestId' => $requestId,
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'refund',
            'vnp_TmnCode' => $tmnCode,
            'vnp_TransactionType' => $transactionType,
            'vnp_TxnRef' => $orderId,
            'vnp_Amount' => $amount * 100,
            'vnp_OrderInfo' => $description,
            'vnp_TransactionNo' => $transactionId,
            'vnp_TransactionDate' => $transactionDate,
            'vnp_CreateBy' => Auth::user()?->email ?? 'admin',
            'vnp_CreateDate' => $createDate,
            'vnp_IpAddr' => $ipAddr,
            'vnp_SecureHash' => $vnpSecureHash,
        ];

        $apiUrl = $this->baseUrl . ($this->config['api_paths']['refund'] ?? '/merchant_webapi/api/transaction');
        $response = $this->makeRequest('POST', $apiUrl, $payload);

        $responseCode = $response['vnp_ResponseCode'] ?? '99';

        if ($responseCode !== '00') {
            throw new PaymentGatewayException(
                PaymentEnum::getVNPayResultMessage($responseCode),
                $this->getGatewayName(),
                $responseCode,
                ['response' => $response]
            );
        }

        return [
            'status' => 'refunded',
            'transaction_id' => (string) ($response['vnp_TransactionNo'] ?? ''),
            'result_code' => $responseCode,
            'message' => 'Hoàn tiền thành công',
            'gateway_response' => $response,
        ];
    }

    /**
     * Verify VNPay IPN signature
     *
     * VNPay gửi IPN qua GET — hash = HMAC-SHA512 của sorted params (trừ vnp_SecureHash)
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        $receivedHash = $payload['vnp_SecureHash'] ?? '';
        if (empty($receivedHash)) {
            return false;
        }

        // Loại bỏ hash fields trước khi tính
        $inputData = $payload;
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        // Sort theo key
        ksort($inputData);

        $hashData = http_build_query($inputData);
        $expectedHash = $this->buildSignature($hashData);

        return hash_equals($expectedHash, $receivedHash);
    }

    /**
     * Normalize VNPay IPN payload về format chuẩn
     */
    public function parseWebhookPayload(array $payload): array
    {
        $responseCode = $payload['vnp_ResponseCode'] ?? '99';
        $transactionStatus = $payload['vnp_TransactionStatus'] ?? $responseCode;

        return [
            'gateway_order_id' => $payload['vnp_TxnRef'] ?? '',
            'gateway_transaction_id' => (string) ($payload['vnp_TransactionNo'] ?? ''),
            'result_code' => $responseCode,
            'is_success' => $responseCode === '00' && $transactionStatus === '00',
            'amount' => (int) (($payload['vnp_Amount'] ?? 0) / 100), // VNPay gửi ×100
            'message' => PaymentEnum::getVNPayResultMessage($responseCode),
        ];
    }

    // Signature — HMAC SHA512

    /**
     * HMAC-SHA512 — VNPay dùng SHA512 (khác MoMo SHA256)
     */
    protected function buildSignature(string $rawData): string
    {
        $hashSecret = $this->config['hash_secret'];
        return hash_hmac('sha512', $rawData, $hashSecret);
    }

    /**
     * Timing-safe comparison
     */
    protected function verifySignature(string $rawData, string $signature): bool
    {
        return hash_equals($this->buildSignature($rawData), $signature);
    }

    // Helpers

    /**
     * Map VNPay response code sang internal status
     */
    protected function mapResponseCodeToStatus(string $code): string
    {
        return match ($code) {
            '00' => PaymentEnum::STATUS_COMPLETED,
            '01' => PaymentEnum::STATUS_PENDING,
            '02' => PaymentEnum::STATUS_FAILED, // merchant denied
            default => PaymentEnum::STATUS_FAILED,
        };
    }

    /**
     * VNPay yêu cầu orderInfo không có dấu và ký tự đặc biệt
     */
    protected function sanitizeOrderInfo(string $text): string
    {
        // Bỏ dấu tiếng Việt
        $text = $this->removeVietnameseTones($text);
        // Chỉ giữ alphanumeric, space, dấu chấm, gạch ngang
        return preg_replace('/[^A-Za-z0-9\s.\-]/', '', $text);
    }

    protected function removeVietnameseTones(string $str): string
    {
        $from = 'àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ';
        $to = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeiiiiioooooooooooooooooouuuuuuuuuuuyyyyydAAAAAAAAAAAAAAAAAEEEEEEEEEEEIIIIIOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYYD';

        return str_replace(
            mb_str_split($from),
            mb_str_split($to),
            $str
        );
    }
}
