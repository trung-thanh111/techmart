<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasQuery;

class Order extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'code',
        'user_id',
        'customer_email',
        'customer_phone',
        'customer_name',
        'subtotal',
        'tax_amount',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'shipping_method',
        'shipping_provider',
        'tracking_number',
        'shipping_address',
        'shipping_city',
        'shipping_ward',
        'shipping_street',
        'shipping_country_code',
        'shipping_postal_code',
        'customer_note',
        'admin_note',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'refunded_at',
        'paid_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'order_items',
        'order_status_histories',
        'order_shipments',
        'payment_transactions',
        'order_refunds'
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function order_status_histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function order_shipments()
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function payment_transactions()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function order_refunds()
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->shipping_street,
            $this->shipping_ward,
            $this->shipping_city,
            $this->shipping_address,
        ]);

        return implode(', ', $parts);
    }
}
