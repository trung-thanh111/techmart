<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FaqCatalogue extends Model
{
    use HasQuery, SoftDeletes;

    protected $table = 'faq_catalogues';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_hot',
        'order',
        'user_id',
        'publish'
    ];

    protected $casts = [
        'is_hot' => 'boolean',
    ];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [
        'faq_catalogues',
        'faqs'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'faq_catalogue_id', 'id');
    }

    public function faq_catalogues(): BelongsToMany
    {
        return $this->belongsToMany(FaqCatalogue::class, 'related_faq_catalogues', 'faq_catalogue_id', 'related_faq_catalogue_id');
    }
}
