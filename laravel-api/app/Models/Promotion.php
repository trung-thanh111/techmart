<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;

class Promotion extends Model
{
    use HasQuery;

    protected $table = 'promotions';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'start_date',
        'end_date',
        'priority ',
        'can_combine',
        'used_count',
        'use_limit',
        'promotion_catalogue_id',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'type' => "enum",
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];



}
