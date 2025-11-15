<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasQuery;

class PromotionTarget extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'promotion_targets';

    protected $fillable = [
        'promotion_id',
        'product_id',
        'product_variant_id',
        'product_catalogue_id',
        'is_gift',
        'min_quantity',
        'max_quantity',
    ];

    protected $casts = [
        'is_gift' => 'boolean',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function product_variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    public function product_catalogue(): BelongsTo
    {
        return $this->belongsTo(ProductCatalogue::class, 'product_catalogue_id', 'id');
    }
}
