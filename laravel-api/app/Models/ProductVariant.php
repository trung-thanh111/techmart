<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'agencies'
    ];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variant_attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_attribute');
    }

    public function inventorys(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_variant_id', 'id');
    }

    public function agencies()
    {
        return $this->hasManyThrough(
            Agency::class,
            Inventory::class,
            'product_variant_id',
            'id',
            'id',
            'agency_id'
        )->where('is_visible', true)->distinct();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'product_variant_id', 'id');
    }

    public function cart_items(): MorphMany
    {
        return $this->morphMany(CartItem::class, 'purchasable');
    }
}
