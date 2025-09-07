<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasQuery;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'image',
        'price',
        'made_in',
        'icon',
        'album',
        'extra_album',
        'order',
        'product_catalogue_id',
        'brand_id',
        'user_id',
        'publish',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'index',
        'scheme',
        'iframe',
    ];

    protected $casts = [
        'album' => 'json',
        'extra_album' => 'json'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client -> phân bổ data quan hệ n-n
     *
     * @var array
     */
    public $relationable = [
        'product_catalogues',
    ];

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

    public function product_variants(): HasMany {
        return $this->hasMany(ProductVariant::class);
    }
}
