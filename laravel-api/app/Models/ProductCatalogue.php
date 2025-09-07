<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCatalogue extends Model
{
    use HasQuery;

    protected $table = 'product_catalogues';

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'order',
        'publish',
        'user_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'index',
        'scheme',
        'iframe',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'users',
    ];

    protected $casts = [
        'album' => 'json',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
