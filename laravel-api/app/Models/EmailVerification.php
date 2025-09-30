<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verifications';

    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'is_used',
        'payload'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
        'payload' => 'array'
    ];

    /**
     * Kiểm tra token có hết hạn không
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Kiểm tra token có hợp lệ không (chưa hết hạn và chưa sử dụng)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->is_used;
    }

    /**
     * Đánh dấu token đã được sử dụng
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Scope để lấy token hợp lệ
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope để lấy token theo email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}
