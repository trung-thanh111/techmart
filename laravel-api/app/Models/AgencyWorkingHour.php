<?php
namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgencyWorkingHour extends Model
{
    use HasQuery;

    protected $table = 'agency_working_hours';

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'publish'
    ];

    protected $casts = [
        'open_time' => 'datetime:H:i', 
        'close_time' => 'datetime:H:i', 
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'agencies'
    ];

    public function agencies(): BelongsToMany
    {
        return $this->belongsToMany(Agency::class, 'agency_working_hour_agency', 'agency_working_hour_id', 'agency_id');
    }

}
