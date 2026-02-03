<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class OrderPayment extends Model
{
    use HasFactory, HasQuery;

    protected $table = 'order_payments';

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'payment_gateway',
        'amount',
        'currency',
        'status',
        'card_brand',
        'card_last4',
        'gateway_response',
        'failure_reason',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'gateway_response' => 'json',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_refunds()
    {
        return $this->hasMany(OrderRefund::class, 'payment_id');
    }
}
