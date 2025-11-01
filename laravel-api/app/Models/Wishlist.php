<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasQuery;

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'product_variant_id'
    ];

    protected $casts = [];

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

    public function product_variants(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }
}
