<?php

namespace App\Models;

use App\Traits\HasQuery;
use App\Traits\HasTransaction;
use App\Traits\HasUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasQuery;

    protected $table = 'banners';

    protected $fillable = [
        'name',
        'slug',
        'album',
        'type',
        'position',
        'start_at',
        'end_at',
        'order',
        'link_url',
        'is_hot',
        'publish',
        'user_id'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_hot' => 'boolean',
        'album' => 'array',
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
}
