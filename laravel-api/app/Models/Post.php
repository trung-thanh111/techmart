<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasQuery;

    protected $table = 'posts';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'image',
        'table_of_content',
        'order',
        'is_hot',
        'view_count',
        'post_catalogue_id',
        'tag_id',
        'publish',
        'user_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'published_at',
    ];

    protected $casts = [
        'album' => 'json',
        'is_hot' => 'boolean',
        'table_of_content' => 'boolean',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'post_catalogues',
        'relateds',
        'post_tags',
        'users'
    ];

    public function post_catalogues(): BelongsToMany
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_post');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post_tags(): BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'post_tag_post', 'post_id', 'post_tag_id');
    }

    public function relateds(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'related_posts', 'post_id', 'related_post_id');
    }

    public function post_comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id', 'id');
    }
}
