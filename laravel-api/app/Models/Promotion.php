<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasQuery;

class Promotion extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = [
        'name',
        'coupon_code',
        'description',
        'promotion_catalogue_id',
        'discount_value',
        'discount_type',
        'max_discount_value',
        'start_date',
        'end_date',
        'priority',
        'can_combine',
        'used_count',
        'publish',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'can_combine' => 'boolean',
        'priority' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    public function promotion_targets(): HasMany
    {
        return $this->hasMany(PromotionTarget::class, 'promotion_id', 'id');
    }

    public function promotion_catalogues(): BelongsTo
    {
        return $this->belongsTo(PromotionCatalogue::class, 'promotion_catalogue_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_targets', 'promotion_id', 'product_id')
            ->whereNull('promotion_targets.product_variant_id')
            ->whereNull('promotion_targets.product_catalogue_id')
            ->wherePivot('is_gift', false);
    }

    public function product_variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'promotion_targets', 'promotion_id', 'product_variant_id')
            ->whereNull('promotion_targets.product_id')
            ->whereNull('promotion_targets.product_catalogue_id')
            ->wherePivot('is_gift', false);
    }

    public function product_catalogues(): BelongsToMany
    {
        return $this->belongsToMany(ProductCatalogue::class, 'promotion_targets', 'promotion_id', 'product_catalogue_id')
            ->whereNull('promotion_targets.product_id')
            ->whereNull('promotion_targets.product_variant_id')
            ->wherePivot('is_gift', false);
    }
}
