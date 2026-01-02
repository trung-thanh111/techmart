<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasQuery;

class Menu extends Model
{
    use HasQuery;

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'slug',
        'position',
        'publish'
    ];

    protected $casts = [
        'publish' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = ['menu_items'];

    public function menu_items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
