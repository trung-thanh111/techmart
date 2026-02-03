<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasQuery;

class OrderItem extends Model
{
    use HasFactory, HasQuery;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_sku',
        'product_name',
        'product_description',
        'product_attributes',
        'product_image',
        'quantity',
        'unit_price',
        'original_price',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'total',
        'warranty_months',
        'warranty_expires_at',
        'status',
        'is_returnable',
        'return_day',
        'note',
    ];

    protected $casts = [
        'product_attributes' => 'json',
        'warranty_expires_at' => 'date',
        'is_returnable' => 'boolean',
        'quantity' => 'integer',
        'warranty_months' => 'integer',
        'return_day' => 'integer',
        'unit_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_variants()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
