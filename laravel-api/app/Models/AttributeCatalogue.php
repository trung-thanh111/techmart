<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeCatalogue extends Model
{
    use HasQuery;

    protected $table = 'attribute_catalogues';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'user_id',
        'publish'
    ];

    protected $casts = [];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'attributes'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'attribute_catalogue_id', 'id');
    }

    public function attributes_with_product_count(): HasMany
    {
        return $this->hasMany(Attribute::class, 'attribute_catalogue_id', 'id')
            ->withCount(['product_variants as product_count' => function ($query) {
                // Đếm sản phẩm (distinct product_id) thông qua variant
                $query->select(\Illuminate\Support\Facades\DB::raw('count(distinct product_variants.product_id)'))
                    ->join('products', 'products.id', '=', 'product_variants.product_id')
                    ->where('products.publish', 2)
                    ->whereNull('products.deleted_at')
                    ->where('product_variants.publish', 2);
            }]);
    }
}
