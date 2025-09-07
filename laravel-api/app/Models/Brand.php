<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brand extends Model
{
    use HasQuery;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'country',
        'order',
        'publish',
        'user_id',
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
