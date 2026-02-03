<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class OrderRefund extends Model
{
    use HasFactory, HasQuery;

    protected $table = 'order_refunds';

    protected $fillable = [
        'order_id',
        'payment_id',
        'refund_number',
        'amount',
        'currency',
        'reason',
        'reason_description',
        'status',
        'approved_by',
        'approved_at',
        'refund_items',
    ];

    protected $casts = [
        'refund_items' => 'json',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_payments()
    {
        return $this->belongsTo(OrderPayment::class, 'payment_id');
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
