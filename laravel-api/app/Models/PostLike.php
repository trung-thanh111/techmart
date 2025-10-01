<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostLike extends Model
{
    use HasQuery;

    protected $table = 'post_likes';

    protected $fillable = ['user_id', 'post_id'];

    protected $casts = [];

    public $relationable = [];

    /**
     * Booted events
     */
    protected static function booted(): void
    {
        // Khi tạo mới like -> tăng like_count
        static::created(function (PostLike $postLike) {
            Post::where('id', $postLike->post_id)->increment('like_count');
        });

        // Khi xóa like -> giảm like_count
        static::deleted(function (PostLike $postLike) {
            Post::where('id', $postLike->post_id)->decrement('like_count');
        });
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function posts(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
