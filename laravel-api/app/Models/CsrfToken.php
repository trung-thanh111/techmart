<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsrfToken extends Model
{
    protected $table = 'csrf_tokens';

    protected $fillable = [
        'user_id',
        'csrf_token',
    ];
}
