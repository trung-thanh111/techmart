<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasQuery;

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'user_id',
        'attribute_catalogue_id',
        'publish'
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

    public function attributes(): HasMany{
        return $this->hasMany(Attribute::class, 'attribute_catalogue_id', 'id');
    }
}
