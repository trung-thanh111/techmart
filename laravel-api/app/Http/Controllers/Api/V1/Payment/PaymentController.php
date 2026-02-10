<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Http\Requests\Payment\RefundPaymentRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\Payment\PaymentServiceInterface;
use App\Exceptions\Payment\PaymentException;
use Illuminate\Http\JsonResponse;

/**
 * Controller xử lý các endpoint thanh toán (cần auth)
 *
 * Endpoints:
 * POST   /api/v1/payment/create       → Tạo yêu cầu thanh toán
 * GET    /api/v1/payment/{orderId}    → Truy vấn trạng thái thanh toán
 * POST   /api/v1/payment/refund       → Hoàn tiền (admin)
 * GET    /api/v1/payment/health       → Health check
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentServiceInterface $paymentService
    ) {}

    /**
     * Tạo yêu cầu thanh toán
     * Trả về payUrl để FE redirect user sang gateway
     *
     * POST /api/v1/payment/create
     * Body: { order_id: int, gateway: 'momo'|'vnpay' }
     */
    public function create(CreatePaymentRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->createPayment(
                $request->input('order_id'),
                $request->input('gateway')
            );

            return ApiResource::ok($result, 'Tạo yêu cầu thanh toán thành công');
        } catch (PaymentException $e) {
            return ApiResource::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Truy vấn trạng thái thanh toán
     *
     * GET /api/v1/payment/{orderId}
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            $result = $this->paymentService->queryPaymentStatus($orderId);
            return ApiResource::ok($result, 'Truy vấn trạng thái thanh toán thành công');
        } catch (\Throwable $e) {
            return ApiResource::error($e->getMessage(), 500);
        }
    }

    /**
     * Hoàn tiền cho đơn hàng (admin only)
     *
     * POST /api/v1/payment/refund
     * Body: { order_id: int, amount: float, description: string }
     */
    public function refund(RefundPaymentRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->refundPayment(
                $request->input('order_id'),
                $request->input('amount'),
                $request->input('description')
            );

            return ApiResource::ok($result, 'Hoàn tiền thành công');
        } catch (PaymentException $e) {
            return ApiResource::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Health check endpoint
     * Dùng cho monitoring — kiểm tra service payment còn hoạt động
     *
     * GET /api/v1/payment/health
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'payment',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
