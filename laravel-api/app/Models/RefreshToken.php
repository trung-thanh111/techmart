<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_tokens';
    
    protected $fillable = [
        'user_id',
        'refresh_token',
        'expires_at',
        'was_used',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'was_used' => 'boolean',
        'is_revoked' => 'boolean',
    ];
}
