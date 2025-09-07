<?php
namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'value',
        'module',
        'title',
        'user_id',
        'publish'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'user_catalogues'
    ];

    public function user_catalogues(): BelongsToMany{
        return $this->belongsToMany(UserCatalogue::class, 'user_catalogue_permission');
    }

}
