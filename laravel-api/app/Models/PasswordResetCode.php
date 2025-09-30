<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordResetCode extends Model
{
    use HasFactory;

    protected $table = 'password_reset_codes';

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'is_used',
        'attempts'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }
}
