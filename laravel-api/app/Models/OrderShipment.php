<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class OrderShipment extends Model
{
    use HasFactory, HasQuery;

    protected $table = 'order_shipments';

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'service_type',
        'status',
        'weight',
        'dimensions',
        'package_count',
        'shipping_cost',
        'estimated_delivery_at',
        'picked_up_at',
        'delivered_at',
        'receiver_name',
        'delivery_note',
        'tracking_data',
    ];

    protected $casts = [
        'dimensions' => 'json',
        'tracking_data' => 'json',
        'estimated_delivery_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'package_count' => 'integer',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }
}
