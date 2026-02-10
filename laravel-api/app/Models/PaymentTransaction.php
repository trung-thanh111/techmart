<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model PaymentTransaction
 * Lưu trữ chi tiết mỗi lần gọi payment gateway
 * Hỗ trợ idempotency thông qua request_id unique
 */
class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id',
        'order_payment_id',
        'gateway',
        'transaction_type',
        'gateway_transaction_id',
        'request_id',
        'amount',
        'currency',
        'status',
        'gateway_request',
        'gateway_response',
        'signature',
        'pay_url',
        'deeplink',
        'qr_code_url',
        'paid_at',
        'expired_at',
        'error_code',
        'error_message',
        'retry_count',
        'last_retry_at',
        'metadata',
    ];

    protected $casts = [
        'gateway_request' => 'json',
        'gateway_response' => 'json',
        'metadata' => 'json',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_retry_at' => 'datetime',
    ];

    /**
     * Ẩn các field nhạy cảm khi serialize
     */
    protected $hidden = [
        'signature',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_payment()
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id');
    }

    // Scopes
    /**
     * Lọc theo gateway cụ thể
     */
    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Lọc transactions đang chờ (pending)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Lọc transactions đã hết hạn nhưng chưa cập nhật
     */
    public function scopeExpiredPending($query)
    {
        return $query->where('status', 'pending')
            ->where('expired_at', '<', now());
    }

    // Helpers
    /**
     * Kiểm tra transaction đã hoàn thành chưa
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Kiểm tra transaction có thể retry không
     */
    public function canRetry(int $maxRetries = 3): bool
    {
        return $this->status === 'failed'
            && $this->retry_count < $maxRetries;
    }

    /**
     * Kiểm tra transaction đã hết hạn chưa
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }
}
