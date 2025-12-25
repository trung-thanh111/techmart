<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PromotionRule
 *
 * Lưu định nghĩa điều kiện áp dụng cho Promotion.
 *
 * @package App\Models
 */
class PromotionRule extends Model
{
    protected $table = 'promotion_rules';

    protected $fillable = [
        'promotion_id',
        'rule_group',
        'logic_operator',
        'rule_type',
        'operator',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Relation: rule thuộc về promotion.
     *
     * @return BelongsTo
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    /**
     * Accessor: parsed_value - parse giá trị rule theo rule_type để sử dụng trong business logic.
     *
     * @return mixed
     */
    public function getParsedValueAttribute()
    {
        $value = $this->value;
        if (empty($value)) {
            return $value;
        }

        switch ($this->rule_type) {
            case 'min_order_total':
                // giá trị có thể là string hoặc number
                return is_array($value) ? $value : ['amount' => (float)$value];
            case 'time_slot':
                // value dạng {"from":"09:00","to":"12:00"}
                return [
                    'from' => $value['from'] ?? null,
                    'to' => $value['to'] ?? null,
                ];
            case 'product_qty_by_category':
            case 'product_qty':
                return $value;
            default:
                return $value;
        }
    }
}
