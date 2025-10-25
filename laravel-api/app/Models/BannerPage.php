<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BannerPage extends Model
{
    use HasQuery;

    protected $table = 'banner_pages';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'publish'
    ];

    protected $casts = [];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'page_id');
    }
}
