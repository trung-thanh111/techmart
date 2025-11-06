<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    use HasQuery;

    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'price',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    public function carts(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }


    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
}
