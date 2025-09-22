<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostCatalogue extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'post_catalogues';

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'image',
        'order',
        'publish',
        'is_hot',
        'user_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
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
        'is_hot' => 'boolean',
    ];
    
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
