<?php

namespace App\Models;

use App\Traits\HasQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasQuery;

    protected $table = 'inventorys';

    protected $fillable = [
        'product_variant_id',
        'agency_id',
        'stock',
        'price_override',
        'publish',
    ];

    protected $casts = [];

    /**
     * Biến này đăng ký quan hệ được phép bắn lên từ client
     *
     * @var array
     */
    public $relationable = [];

    /**
     * The "booted" method of the model.
     * khi mà thêm sản phẩm tự động pick data sang bảng productvariant field stock
     */
    // protected static function booted(): void
    // {
    //     // Khi tạo mới inventory
    //     static::created(function (Inventory $inventory) {
    //         DB::transaction(function () use ($inventory) {
    //             $productVariant = ProductVariant::find($inventory->product_variant_id);
    //             if ($productVariant) {
    //                 $productVariant->increment('stock', $inventory->stock);
    //             }
    //         });
    //     });

    //     // Khi update inventory
    //     static::updated(function (Inventory $inventory) {
    //         DB::transaction(function () use ($inventory) {
    //             $oldStock = $inventory->getOriginal('stock');
    //             $newStock = $inventory->stock;
    //             $stockDifference = $newStock - $oldStock;

    //             // Nếu có thay đổi product_variant_id
    //             $oldProductVariantId = $inventory->getOriginal('product_variant_id');
    //             $newProductVariantId = $inventory->product_variant_id;

    //             if ($oldProductVariantId != $newProductVariantId) {
    //                 // Trừ stock từ variant cũ
    //                 $oldVariant = ProductVariant::find($oldProductVariantId);
    //                 if ($oldVariant) {
    //                     $oldVariant->decrement('stock', $oldStock);
    //                 }

    //                 // Cộng stock vào variant mới
    //                 $newVariant = ProductVariant::find($newProductVariantId);
    //                 if ($newVariant) {
    //                     $newVariant->increment('stock', $newStock);
    //                 }
    //             } else {
    //                 // Chỉ update stock cho cùng variant
    //                 if ($stockDifference != 0) {
    //                     $productVariant = ProductVariant::find($inventory->product_variant_id);
    //                     if ($productVariant) {
    //                         if ($stockDifference > 0) {
    //                             $productVariant->increment('stock', $stockDifference);
    //                         } else {
    //                             $productVariant->decrement('stock', abs($stockDifference));
    //                         }
    //                     }
    //                 }
    //             }
    //         });
    //     });

    //     // Khi xóa inventory
    //     static::deleted(function (Inventory $inventory) {
    //         DB::transaction(function () use ($inventory) {
    //             $productVariant = ProductVariant::find($inventory->product_variant_id);
    //             if ($productVariant) {
    //                 $productVariant->decrement('stock', $inventory->stock);
    //             }
    //         });
    //     });
    // }

    public function product_variants(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function agencies(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
