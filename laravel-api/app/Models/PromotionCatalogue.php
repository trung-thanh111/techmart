<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionCatalogue extends Model
{
    use HasQuery;

    protected $table = 'promotion_catalogues';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'order',
        'publish',
        'user_id',
    ];

    protected $casts = [];

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

    /**
     * Scope: chỉ lấy những catalogue được publish.
     *
     * @param $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->where('publish', 2);
    }
}
