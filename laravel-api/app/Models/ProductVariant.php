<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasQuery;

    protected $table = 'product_variants';

    protected $fillable = [
        'name',
        'url',
        'code',
        'stock',
        'sku',
        'price',
        'barcode',
        'attribute_data',
        'product_id',
        'publish',
    ];

    protected $casts = [
        'attribute_data' => 'array',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'variant_attributes',
        'availableAgencies'
    ];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant_attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_attribute');
    }


    /**
     * Lấy danh sách agencies có sẵn variant này (stock > 1 và isVisible = true)
     */
    public function availableAgencies(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'inventorys', 'product_variant_id', 'agency_id')
            ->withPivot('stock', 'price_override', 'publish')
            ->wherePivot('stock', '>', 1)
            ->where('agencies.is_visible', true)
            ->where('agencies.publish', 2)->get();
    }
}
