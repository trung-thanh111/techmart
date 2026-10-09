<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasQuery;
use App\Enums\Promotion as PromotionEnum;

/**
 * Class Promotion
 *
 * Model chính cho chương trình khuyến mãi.
 * - Các quan hệ đặt theo tên snake_case (hasMany: có s).
 *
 * @package App\Models
 */
class Promotion extends Model
{
    use HasQuery;

    protected $table = 'promotions';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'image',
        'type',
        'priority',
        'promotion_catalogue_id',
        'can_combine',
        'combinable_with',
        'usage_limit',
        'usage_per_customer',
        'used_count',
        'start_date',
        'end_date',
        'publish',
    ];

    /**
     * Các kiểu ép (casts).
     *
     * @var array
     */
    protected $casts = [
        'combinable_with' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'publish' => 'integer',
        'can_combine' => 'boolean',
    ];

    /**
     * Thuộc tính ảo trả về trong JSON.
     *
     * @var array
     */
    protected $appends = [
        'remaining_usage',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'promotion_rules',
        'promotion_actions',
        'promotion_targets',
    ];

    /**
     * Relation: Promotion thuộc về PromotionCatalogue.
     *
     * @return BelongsTo
     */
    public function promotion_catalogue(): BelongsTo
    {
        return $this->belongsTo(PromotionCatalogue::class, 'promotion_catalogue_id', 'id');
    }

    /**
     * Relation: Promotion có nhiều rules.
     *
     * @return HasMany
     */
    public function promotion_rules(): HasMany
    {
        return $this->hasMany(PromotionRule::class, 'promotion_id', 'id');
    }

    /**
     * Relation: Promotion có nhiều actions.
     *
     * @return HasMany
     */
    public function promotion_actions(): HasMany
    {
        return $this->hasMany(PromotionAction::class, 'promotion_id', 'id')->orderBy('execution_order', 'asc');
    }

    /**
     * Relation: Promotion có nhiều targets.
     *
     * @return HasMany
     */
    public function promotion_targets(): HasMany
    {
        return $this->hasMany(PromotionTarget::class, 'promotion_id', 'id');
    }

    /**
     * Relation: Promotion có nhiều usages.
     *
     * @return HasMany
     */
    public function promotion_usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class, 'promotion_id', 'id');
    }


    /**
     * Accessor: số lần còn lại có thể dùng (null = unlimited).
     *
     * @return int|null
     */
    public function getRemainingUsageAttribute(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }
        $remaining = (int)$this->usage_limit - (int)$this->used_count;
        return $remaining < 0 ? 0 : $remaining;
    }

    /**
     * Scope: chỉ lấy các chương trình active.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('publish', PromotionEnum::PUBLISH_PUBLIC)
            ->when($now, fn($q) => $q->where(function ($q2) use ($now) {
                $q2->whereNull('start_date')->orWhere('start_date', '<=', $now);
            }))
            ->when($now, fn($q) => $q->where(function ($q2) use ($now) {
                $q2->whereNull('end_date')->orWhere('end_date', '>=', $now);
            }));
    }

    /**
     * Scope: lọc theo type.
     *
     * @param $query
     * @param string|array $type
     * @return mixed
     */
    public function scopeOfType($query, $type)
    {
        if (is_array($type)) {
            return $query->whereIn('type', $type);
        }
        return $query->where('type', $type);
    }

    /**
     * Scope: có usage left (nếu usage_limit null => unlimited = true).
     *
     * @param $query
     * @return mixed
     */
    public function scopeHasUsageLeft($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
        });
    }
}
