<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class OrderStatusHistory extends Model
{
    use HasFactory, HasQuery;

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'user_id',
        'user_type',
        'note',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
