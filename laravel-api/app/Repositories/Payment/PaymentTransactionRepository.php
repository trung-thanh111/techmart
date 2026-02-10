<?php

namespace App\Repositories\Payment;

use App\Models\PaymentTransaction;
use App\Repositories\BaseRepository;

/**
 * Repository cho PaymentTransaction
 * Cung cấp các query đặc thù cho payment
 */
class PaymentTransactionRepository extends BaseRepository
{
    protected $model;

    public function __construct(PaymentTransaction $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    /**
     * Tìm transaction theo request_id (idempotency key)
     * Dùng để kiểm tra request đã được xử lý chưa
     */
    public function findByRequestId(string $requestId): ?PaymentTransaction
    {
        return $this->model->where('request_id', $requestId)->first();
    }

    /**
     * Tìm tất cả transactions của 1 order
     */
    public function findByOrderId(int $orderId, ?string $status = null)
    {
        $query = $this->model->where('order_id', $orderId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Tìm transaction pending gần nhất của order
     * Dùng khi user quay lại thanh toán — trả lại payUrl cũ nếu chưa hết hạn
     */
    public function findPendingByOrderId(int $orderId, string $gateway): ?PaymentTransaction
    {
        return $this->model
            ->where('order_id', $orderId)
            ->where('gateway', $gateway)
            ->where('status', 'pending')
            ->where('transaction_type', 'payment')
            ->where(function ($q) {
                // Chỉ lấy transaction chưa hết hạn
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Tìm transaction completed gần nhất (để refund)
     */
    public function findCompletedByOrderId(int $orderId): ?PaymentTransaction
    {
        return $this->model
            ->where('order_id', $orderId)
            ->where('status', 'completed')
            ->where('transaction_type', 'payment')
            ->orderBy('paid_at', 'desc')
            ->first();
    }

    /**
     * Tìm các transaction đã hết hạn nhưng vẫn pending
     * Dùng cho scheduled job cleanup
     */
    public function findExpiredPending(int $limit = 100)
    {
        return $this->model
            ->where('status', 'pending')
            ->where('expired_at', '<', now())
            ->orderBy('expired_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Tìm transaction theo gateway order_id (MoMo orderId)
     * Dùng khi nhận webhook — MoMo gửi orderId mà ta đã tạo
     */
    public function findByGatewayOrderId(string $gatewayOrderId, string $gateway): ?PaymentTransaction
    {
        // orderId gửi cho MoMo được lưu trong gateway_request->orderId
        // Hoặc ta dùng request_id chính là orderId gửi cho MoMo
        return $this->model
            ->where('gateway', $gateway)
            ->where('request_id', $gatewayOrderId)
            ->first();
    }
}
