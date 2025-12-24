<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasQuery;

class Popup extends Model
{
    use HasQuery;

    protected $table = 'popups';

    protected $fillable = [
        'title',
        'type',
        'slug',
        'content',
        'image',
        'link',
        'button_text',
        'form_html',
        'show_delay',
        'priority',
        'publish'
    ];

    protected $casts = [
        'show_delay' => 'integer',
        'priority' => 'boolean',
        'publish' => 'integer',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];
}
