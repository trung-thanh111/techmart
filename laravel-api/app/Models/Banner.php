<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasQuery;

    protected $table = 'banners';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'type',
        'position',
        'device',
        'display_mode',
        'start_at',
        'end_at',
        'sort_order',
        'link_url',
        'width',
        'height',
        'alt',
        'class',
        'loading',
        'publish',
        'user_id'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'publish' => 'integer',
        'sort_order' => 'integer',
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
