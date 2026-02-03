<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressOrder extends Model
{
    use HasQuery;

    protected $table = 'address_orders';

    protected $fillable = [
        'title',
        'slug',
        'full_name',
        'phone',
        'email',
        'address',
        'city',
        'ward',
        'street',
        'postal_code',
        'country_code',
        'is_default',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'is_default' => 'boolean',
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

    public function limit_record_per_user(): bool
    {
        return $this->where('user_id', $this->user_id)->count() < 3;
    }
}
