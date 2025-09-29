<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostCommentLike extends Model
{
    use HasQuery, HasFactory;

    
    protected $fillable = [
        'user_id',
        'comment_id',
    ];

    protected $casts = [];
    
    /**
     * Các quan hệ cho phép client yêu cầu load kèm (an toàn)
     */
    public $relationable = [];

    /**
     * Get the user that liked the comment.
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the comment that was liked.
     */
    public function post_comments(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'comment_id', 'id');
    }
}
