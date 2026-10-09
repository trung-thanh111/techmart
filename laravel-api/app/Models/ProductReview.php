<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'user_name',
        'user_email',
        'user_avatar',
        'rating',
        'comment',
        'images',
        'publish',
        'likes_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'publish' => 'integer',
        'likes_count' => 'integer',
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $relationable = [
        'product',
        'user',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopePublished($query)
    {
        return $query->where('publish', 2);
    }
}
