<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'menu_items';

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'name',
        'slug',
        'icon',
        'order',
        'menu_id',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'lft' => 'integer',
        'rgt' => 'integer',
        'level' => 'integer',
        'order' => 'integer',
        'menu_id' => 'integer',
        'user_id' => 'integer',
        'publish' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = ['children', 'parent', 'menu'];

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
