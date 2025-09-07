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
        'icon',
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

    public function users(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
