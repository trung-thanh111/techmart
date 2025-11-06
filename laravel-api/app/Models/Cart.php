<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasQuery;

    protected $table = 'carts';

    protected $fillable = [
        'customer_id',
        'guest_id',
        'merged_id',
        'coupon_code',
        'meta',
        'publish'
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

    public function cart_items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
}
