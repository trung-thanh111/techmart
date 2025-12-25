<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class PromotionTarget
 *
 * Định nghĩa target áp dụng cho Promotion (order, product, category, ...).
 *
 * @package App\Models
 */
class PromotionTarget extends Model
{
    protected $table = 'promotion_targets';

    protected $fillable = [
        'promotion_id',
        'target_type',
        'target_id',
        'is_included',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_included' => 'boolean',
    ];

    /**
     * Relation: target thuộc về promotion
     *
     * @return BelongsTo
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    /**
     * Morph relation: có thể liên kết tới nhiều loại đối tượng (product, category, ...)
     *
     * @return MorphTo
     */
    public function targetable(): MorphTo
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }

    /**
     * Scope: include targets
     *
     * @param $query
     * @return mixed
     */
    public function scopeIncluded($query)
    {
        return $query->where('is_included', true);
    }

    /**
     * Scope: excluded targets
     *
     * @param $query
     * @return mixed
     */
    public function scopeExcluded($query)
    {
        return $query->where('is_included', false);
    }
}
