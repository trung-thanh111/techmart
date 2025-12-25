<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PromotionUsage
 *
 * Lưu lịch sử usage của promotion (ai dùng, order nào, discount bao nhiêu).
 *
 * @package App\Models
 */
class PromotionUsage extends Model
{
    protected $table = 'promotion_usages';

    protected $fillable = [
        'promotion_id',
        'customer_id',
        'order_id',
        'discount_amount',
        'order_total',
        'applied_rules',
        'applied_actions',
    ];

    protected $casts = [
        'applied_rules' => 'array',
        'applied_actions' => 'array',
        'discount_amount' => 'float',
        'order_total' => 'float',
    ];

    /**
     * Relation: usage thuộc về promotion.
     *
     * @return BelongsTo
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    /**
     * Relation: usage thuộc về khách hàng (user).
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    // /**
    //  * Relation: usage có thể liên kết tới order (nếu có).
    //  *
    //  * @return BelongsTo
    //  */
    // public function order(): BelongsTo
    // {
    //     return $this->belongsTo(Order::class, 'order_id', 'id');
    // }
}
