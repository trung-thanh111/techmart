<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'icon',
        'album',
        'extra_album',
        'table_of_content',
        'order',
        'post_catalogue_id',
        'publish',
        'user_id',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'scheme',
        'iframe',
        'published_at',
    ];

    protected $casts = [
        'album' => 'json'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'post_catalogues',
        'posts'
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

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'related_posts', 'post_id', 'related_post_id');
    }
}
