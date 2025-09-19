<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'slug',
        'answer',
        'order',
        'is_hot',
        'faq_catalogue_id',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'is_hot' => 'boolean'
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'faq_catalogues'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function faq_catalogues(): BelongsTo
    {
        return $this->belongsTo(FaqCatalogue::class, 'faq_catalogue_id', 'id');
    }
}
