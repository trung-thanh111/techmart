<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasQuery;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'content',
        'price_origin',
        'album',
        'season',
        'warranty',
        'sold',
        'sort_order',
        'product_catalogue_id',
        'brand_id',
        'faq_id',
        'tag_id',
        'is_hot',
        'like_count',
        'user_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'publish',
    ];

    protected $casts = [
        'album' => 'array',
        'is_hot' => 'boolean',
        'price_origin' => 'decimal:2',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client -> phân bổ data quan hệ n-n
     *
     * @var array
     */
    public $relationable = [
        'product_catalogues',
        'product_tags',
        'product_faqs',
        'relateds'
    ];
    // todo
    // protected static function booted(): void
    // {
    //     // creating đến gán trước khi insert tối ưu thay vì dùng created 2 lần insert & update
    //     static::creating(function (Product $product) {
    //         // Xử lý productCatalogues từ frontend (camelCase) - fallback nếu Service chưa xử lý
    //         if (!empty($product->productCatalogues) && is_array($product->productCatalogues)) {
    //             $product->product_catalogue_id = $product->productCatalogues[0];
    //         }
    //     });
    // }

    public function product_catalogues(): BelongsToMany
    {
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_product');
    }

    public function brands(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product_variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function product_tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag_product', 'product_id', 'product_tag_id');
    }

    public function product_faqs(): BelongsToMany
    {
        return $this->belongsToMany(Faq::class, 'product_faqs', 'product_id', 'faq_id');
    }

    public function relateds(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function cart_items(): MorphMany
    {
        return $this->morphMany(CartItem::class, 'purchasable');
    }
}
