<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PromotionAction
 *
 * Đại diện các hành động được thực hiện khi promotion được áp dụng (percent, fixed, bxgy, tiered...).
 *
 * @package App\Models
 */
class PromotionAction extends Model
{
    protected $table = 'promotion_actions';

    protected $fillable = [
        'promotion_id',
        'execution_order',
        'action_type',
        'action_value',
        'max_discount',
        'min_discount',
        'apply_to',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'action_value' => 'float',
        'max_discount' => 'float',
        'min_discount' => 'float',
    ];

    /**
     * Relation: action thuộc về promotion.
     *
     * @return BelongsTo
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }
}
