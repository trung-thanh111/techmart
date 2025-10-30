<?php

namespace App\Models;

use App\Traits\HasQuery;
use App\Traits\HasUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agency extends Model
{
    use HasQuery, HasUpload;

    protected $table = 'agencies';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'email',
        'phone',
        'address',
        'iframe_map',
        'is_main',
        'is_online',
        'is_visible',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'agency_working_hours'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function agency_working_hours(): BelongsToMany
    {
        return $this->belongsToMany(AgencyWorkingHour::class, 'agency_working_hour_agency', 'agency_id', 'agency_working_hour_id');
    }
}
