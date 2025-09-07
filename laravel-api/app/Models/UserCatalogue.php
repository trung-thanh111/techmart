<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCatalogue extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'user_catalogues';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'publish'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'users',
        'permissions'
    ];

    public function users():BelongsToMany {
        return $this->belongsToMany(User::class, 'user_catalogue_user');
    }

    public function permissions():BelongsToMany {
        return $this->belongsToMany(Permission::class, 'user_catalogue_permission');
    }
}
