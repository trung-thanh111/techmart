<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;

class System extends Model
{
    use HasQuery;

    protected $table = 'systems';

    protected $fillable = [
        'keyword',
        'content',
        'publish',
        'user_id'
    ];

    protected $casts = [
        'publish' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];
}
