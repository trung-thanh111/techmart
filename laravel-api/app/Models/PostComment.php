<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    use HasQuery;

    protected $table = 'post_comments';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'content',
        'is_hot',
        'user_id',
        'post_id',
        'publish',
    ];

    // Nếu cần casting các field
    protected $casts = [
        'is_hot' => 'boolean',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'user',
        'post',
        'parent',
        'replies',
        'votes',
    ];
    
    public function posts(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Comment cha (quan hệ với field trong bảng)
    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    // commnent con (lấy ra các cmt còn trong bluan)
    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }

    // public function votes(): HasMany
    // {
    //     return $this->hasMany(PostCommentVote::class, 'comment_id', 'id');
    // }

}
