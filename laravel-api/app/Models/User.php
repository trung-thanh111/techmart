<?php

namespace App\Models;

use App\Traits\HasQuery;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasQuery, SoftDeletes;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'users';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'description',
        'birthday',
        'phone',
        'gender',
        'street',
        'city',
        'ward',
        'user_catalogue_id',
        'image',
        'email',
        'password',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'user_catalogues'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    public function user_catalogues(): BelongsToMany
    {
        return $this->belongsToMany(UserCatalogue::class, 'user_catalogue_user');
    }

    public function user_catalogue(): BelongsTo
    {
        return $this->belongsTo(UserCatalogue::class, 'user_catalogue_id', 'id');
    }

    /**
     * Accessor: kiểm tra user có phải admin không
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->user_catalogue?->is_admin ?? false;
    }

    /**
     * Scope: lấy các user admin
     */
    public function scopeAdmin($query)
    {
        return $query->whereHas('user_catalogue', function ($q) {
            $q->where('is_admin', true);
        });
    }

    public function address_orders(): HasMany
    {
        return $this->hasMany(AddressOrder::class, 'user_id', 'id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'user_id', 'id');
    }
}
