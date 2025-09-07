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
        'album',
        'image',
        'code',
        'stock',
        'sku',
        'price',
        'barcode',
        'product_id',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'album' => 'json'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function variant_attributes(): BelongsToMany {
        return $this->belongsToMany(Attribute::class, 'product_variant_attribute');
    }

}
