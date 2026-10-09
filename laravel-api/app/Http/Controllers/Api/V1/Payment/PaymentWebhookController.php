<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\Payment\PaymentServiceInterface;
use App\Exceptions\Payment\PaymentSignatureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý webhook/IPN từ payment gateway
 *
 * KHÔNG CẦN AUTH — gateway gọi trực tiếp
 * Bảo mật qua signature verification
 *
 * POST /api/v1/payment/webhook/momo    → MoMo IPN (POST JSON)
 * GET  /api/v1/payment/webhook/vnpay   → VNPay IPN (GET query params)
 */
class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentServiceInterface $paymentService
    ) {}

    /**
     * MoMo IPN — POST request with JSON body
     *
     * Response 204 = đã nhận, MoMo không retry
     * Response != 204 = MoMo retry sau 1 phút (tối đa 10 lần)
     */
    public function handleMoMo(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::channel($this->getLogChannel())->info(
            '[Webhook/MoMo] Nhận IPN',
            ['orderId' => $payload['orderId'] ?? 'N/A']
        );

        try {
            $this->paymentService->handleWebhook('momo', $payload);
            return response()->json(null, 204);
        } catch (PaymentSignatureException $e) {
            Log::channel($this->getLogChannel())->warning('[Webhook/MoMo] Signature không hợp lệ');
            return response()->json(['message' => 'Invalid signature'], 403);
        } catch (\Throwable $e) {
            Log::channel($this->getLogChannel())->error('[Webhook/MoMo] Lỗi xử lý IPN', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Internal error'], 500);
        }
    }

    /**
     * VNPay IPN — GET request with query params
     *
     * VNPay yêu cầu trả JSON { RspCode, Message }
     * RspCode 00/02 → VNPAY kết thúc
     * RspCode 01/04/97/99 → VNPAY retry (tối đa 10 lần, mỗi 5 phút)
     */
    public function handleVNPay(Request $request): JsonResponse
    {
        $payload = $request->query();

        Log::channel($this->getLogChannel())->info(
            '[Webhook/VNPay] Nhận IPN',
            ['vnp_TxnRef' => $payload['vnp_TxnRef'] ?? 'N/A']
        );

        try {
            $this->paymentService->handleWebhook('vnpay', $payload);

            return response()->json([
                'RspCode' => '00',
                'Message' => 'Confirm Success',
            ]);
        } catch (PaymentSignatureException $e) {
            Log::channel($this->getLogChannel())->warning('[Webhook/VNPay] Checksum không hợp lệ');
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid Checksum',
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->getLogChannel())->error('[Webhook/VNPay] Lỗi xử lý IPN', ['error' => $e->getMessage()]);
            return response()->json([
                'RspCode' => '99',
                'Message' => 'Unknown error',
            ]);
        }
    }

    /**
     * VNPay Return URL — user redirect back sau thanh toán
     * Đây là redirect URL, hiển thị kết quả cho user (không dùng để cập nhật DB)
     */
    public function vnpayReturn(Request $request): JsonResponse
    {
        $payload = $request->query();
        $responseCode = $payload['vnp_ResponseCode'] ?? '99';
        $orderCode = null;

        try {
            if (!empty($payload['vnp_TxnRef']) && !empty($payload['vnp_SecureHash'])) {
                $this->paymentService->handleWebhook('vnpay', $payload);
            }
            if (!empty($payload['vnp_TxnRef'])) {
                $txn = \App\Models\PaymentTransaction::where('request_id', $payload['vnp_TxnRef'])->first();
                if ($txn && $txn->order) {
                    $orderCode = $txn->order->code;
                }
            }
        } catch (\Throwable $e) {
            Log::channel($this->getLogChannel())->warning('[VNPay Return] Lỗi cập nhật: ' . $e->getMessage());
        }

        return response()->json([
            'success' => $responseCode === '00',
            'response_code' => $responseCode,
            'order_id' => $payload['vnp_TxnRef'] ?? null,
            'order_code' => $orderCode,
            'amount' => isset($payload['vnp_Amount']) ? $payload['vnp_Amount'] / 100 : null,
            'message' => $responseCode === '00' ? 'Thanh toán thành công' : 'Thanh toán thất bại',
        ]);
    }

    protected function getLogChannel(): string
    {
        return config('payment.logging.channel', 'stack');
    }
}
