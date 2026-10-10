<?php

namespace App\Services\Impl\Inventory;

use App\Models\Agency;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventorySyncService
{
    /**
     * Đồng bộ inventory cho một variant với tất cả agencies
     * Sử dụng upsert để tránh duplicate và tối ưu performance
     *
     * @param ProductVariant $productVariant
     * @return int Số lượng records được tạo/cập nhật
     */
    public function syncInventoryForVariant(ProductVariant $productVariant): int
    {
        try {
            return DB::transaction(function () use ($productVariant) {
                // Lấy tất cả agencies đang active (publish = 2)
                $agencies = Agency::where('publish', 2)->get(['id']);

                if ($agencies->isEmpty()) {
                    Log::warning('No active agencies found for inventory sync', [
                        'variant_id' => $productVariant->id
                    ]);
                    return 0;
                }

                // Chuẩn bị data cho bulk insert
                $inventoryData = [];
                $now = now();

                foreach ($agencies as $agency) {
                    $inventoryData[] = [
                        'product_variant_id' => $productVariant->id,
                        'agency_id' => $agency->id,
                        'stock' => 1,
                        'price_override' => $productVariant->price, // Lấy từ variant
                        'publish' => 2, // Mặc định publish = 2
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Sử dụng upsert để tránh duplicate
                // Nếu đã tồn tại (product_variant_id, agency_id) thì update
                // Nếu chưa tồn tại thì insert
                $affected = DB::table('inventorys')->upsert(
                    $inventoryData,
                    ['product_variant_id', 'agency_id'], // Unique keys
                    ['price_override', 'updated_at'] // Columns to update nếu đã tồn tại
                );

                Log::info('Inventory synced successfully', [
                    'variant_id' => $productVariant->id,
                    'agencies_count' => $agencies->count(),
                    'affected_rows' => $affected
                ]);

                return $affected;
            });
        } catch (\Exception $e) {
            Log::error('Error syncing inventory for variant', [
                'variant_id' => $productVariant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Đồng bộ inventory cho nhiều variants cùng lúc
     * Sử dụng cho trường hợp batch processing
     *
     * @param array $variantIds Mảng các variant IDs
     * @return int Tổng số records được tạo/cập nhật
     */
    public function syncInventoryForVariants(array $variantIds): int
    {
        try {
            return DB::transaction(function () use ($variantIds) {
                $variants = ProductVariant::whereIn('id', $variantIds)->get(['id', 'price']);
                $agencies = Agency::where('publish', 2)->get(['id']);

                if ($variants->isEmpty() || $agencies->isEmpty()) {
                    Log::warning('No variants or agencies found for bulk sync', [
                        'variant_ids' => $variantIds,
                        'variants_count' => $variants->count(),
                        'agencies_count' => $agencies->count()
                    ]);
                    return 0;
                }

                $inventoryData = [];
                $now = now();

                foreach ($variants as $variant) {
                    foreach ($agencies as $agency) {
                        $inventoryData[] = [
                            'product_variant_id' => $variant->id,
                            'agency_id' => $agency->id,
                            'stock' => 1,
                            'price_override' => $variant->price,
                            'publish' => 2,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                // Chunk để tránh query quá lớn
                $chunks = array_chunk($inventoryData, 500);
                $totalAffected = 0;

                foreach ($chunks as $chunk) {
                    $affected = DB::table('inventorys')->upsert(
                        $chunk,
                        ['product_variant_id', 'agency_id'],
                        ['price_override', 'updated_at']
                    );
                    $totalAffected += $affected;
                }

                Log::info('Bulk inventory sync completed', [
                    'variants_count' => $variants->count(),
                    'agencies_count' => $agencies->count(),
                    'total_records' => count($inventoryData),
                    'affected_rows' => $totalAffected
                ]);

                return $totalAffected;
            });
        } catch (\Exception $e) {
            Log::error('Error in bulk inventory sync', [
                'variant_ids' => $variantIds,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cập nhật price_override khi price của variant thay đổi
     *
     * @param ProductVariant $productVariant
     * @return int Số lượng records được cập nhật
     */
    public function updatePriceOverrideForVariant(ProductVariant $productVariant): int
    {
        try {
            return DB::transaction(function () use ($productVariant) {
                $affected = Inventory::where('product_variant_id', $productVariant->id)
                    ->update([
                        'price_override' => $productVariant->price,
                        'updated_at' => now()
                    ]);

                Log::info('Price override updated for variant', [
                    'variant_id' => $productVariant->id,
                    'new_price' => $productVariant->price,
                    'affected_rows' => $affected
                ]);

                return $affected;
            });
        } catch (\Exception $e) {
            Log::error('Error updating price override', [
                'variant_id' => $productVariant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Xóa inventory records khi variant bị xóa
     *
     * @param ProductVariant $productVariant
     * @return int Số lượng records bị xóa
     */
    public function deleteInventoryForVariant(ProductVariant $productVariant): int
    {
        try {
            return DB::transaction(function () use ($productVariant) {
                $deleted = Inventory::where('product_variant_id', $productVariant->id)
                    ->delete();

                Log::info('Inventory deleted for variant', [
                    'variant_id' => $productVariant->id,
                    'deleted_rows' => $deleted
                ]);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Error deleting inventory', [
                'variant_id' => $productVariant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Đồng bộ lại toàn bộ inventory cho agency mới
     * Sử dụng khi thêm agency mới vào hệ thống
     *
     * @param Agency $agency
     * @return int Số lượng records được tạo
     */
    public function syncInventoryForNewAgency(Agency $agency): int
    {
        try {
            return DB::transaction(function () use ($agency) {
                $variants = ProductVariant::all(['id', 'price']);

                if ($variants->isEmpty()) {
                    Log::warning('No variants found for agency sync', [
                        'agency_id' => $agency->id
                    ]);
                    return 0;
                }

                $inventoryData = [];
                $now = now();

                foreach ($variants as $variant) {
                    $inventoryData[] = [
                        'product_variant_id' => $variant->id,
                        'agency_id' => $agency->id,
                        'stock' => 1,
                        'price_override' => $variant->price,
                        'publish' => 2,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Chunk để tránh query quá lớn
                $chunks = array_chunk($inventoryData, 500);
                $totalAffected = 0;

                foreach ($chunks as $chunk) {
                    $affected = DB::table('inventorys')->upsert(
                        $chunk,
                        ['product_variant_id', 'agency_id'],
                        ['price_override', 'updated_at']
                    );
                    $totalAffected += $affected;
                }

                Log::info('Inventory synced for new agency', [
                    'agency_id' => $agency->id,
                    'variants_count' => $variants->count(),
                    'affected_rows' => $totalAffected
                ]);

                return $totalAffected;
            });
        } catch (\Exception $e) {
            Log::error('Error syncing inventory for new agency', [
                'agency_id' => $agency->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Tự động trừ tồn kho (inventories & product_variants.stock) khi đơn thanh toán/xác nhận.
     * Đảm bảo tính idempotent: chỉ trừ một lần nếu chưa được trừ.
     *
     * @param Order $order
     * @return bool True nếu trừ thành công, false nếu đơn đã được trừ trước đó
     */
    public function deductStockForOrder(Order $order): bool
    {
        if ($order->is_stock_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $freshOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$freshOrder || $freshOrder->is_stock_deducted) {
                return false;
            }

            $items = $freshOrder->order_items()->get();

            foreach ($items as $item) {
                $variantId = $item->product_variant_id;
                $quantity = (int) $item->quantity;

                if (!$variantId || $quantity <= 0) {
                    continue;
                }

                // 1. Trừ stock trên bảng product_variants
                $variant = ProductVariant::where('id', $variantId)->lockForUpdate()->first();
                if ($variant) {
                    $variant->decrement('stock', $quantity);
                }

                // 2. Trừ stock trên bảng inventories theo từng agency
                $this->deductInventoryStock($variantId, $quantity);
            }

            $freshOrder->is_stock_deducted = true;
            $freshOrder->saveQuietly();
            $order->is_stock_deducted = true;

            Log::info("[InventoryAutoSync] Deducted stock for order #{$freshOrder->code} (ID: {$freshOrder->id})");

            return true;
        });
    }

    /**
     * Tự động hoàn lại tồn kho khi đơn bị hủy (cancelled) hoặc hoàn tiền (refunded).
     * Đảm bảo tính idempotent: chỉ hoàn lại nếu đơn đã từng bị trừ kho.
     *
     * @param Order $order
     * @return bool True nếu hoàn thành công, false nếu đơn chưa từng bị trừ kho
     */
    public function releaseStockForOrder(Order $order): bool
    {
        if (!$order->is_stock_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $freshOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$freshOrder || !$freshOrder->is_stock_deducted) {
                return false;
            }

            $items = $freshOrder->order_items()->get();

            foreach ($items as $item) {
                $variantId = $item->product_variant_id;
                $quantity = (int) $item->quantity;

                if (!$variantId || $quantity <= 0) {
                    continue;
                }

                // 1. Hoàn lại stock trên bảng product_variants
                $variant = ProductVariant::where('id', $variantId)->lockForUpdate()->first();
                if ($variant) {
                    $variant->increment('stock', $quantity);
                }

                // 2. Hoàn lại stock trên bảng inventories
                $this->releaseInventoryStock($variantId, $quantity);
            }

            $freshOrder->is_stock_deducted = false;
            $freshOrder->saveQuietly();
            $order->is_stock_deducted = false;

            Log::info("[InventoryAutoSync] Released stock for order #{$freshOrder->code} (ID: {$freshOrder->id})");

            return true;
        });
    }

    /**
     * Trừ tồn kho trong bảng inventories phân bổ qua các agency có sẵn
     */
    protected function deductInventoryStock(int $variantId, int $quantity): void
    {
        $remaining = $quantity;

        // Ưu tiên trừ kho có nhiều hàng nhất
        $inventories = Inventory::where('product_variant_id', $variantId)
            ->where('stock', '>', 0)
            ->orderByDesc('stock')
            ->lockForUpdate()
            ->get();

        foreach ($inventories as $inv) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min((int) $inv->stock, $remaining);
            $inv->decrement('stock', $deduct);
            $remaining -= $deduct;
        }

        // Nếu các kho có stock > 0 không đủ bù số lượng, trừ vào kho đầu tiên (nếu tồn tại)
        if ($remaining > 0) {
            $firstInv = Inventory::where('product_variant_id', $variantId)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if ($firstInv) {
                $firstInv->decrement('stock', $remaining);
            }
        }
    }

    /**
     * Hoàn lại tồn kho trong bảng inventories
     */
    protected function releaseInventoryStock(int $variantId, int $quantity): void
    {
        $inv = Inventory::where('product_variant_id', $variantId)
            ->orderByDesc('stock')
            ->lockForUpdate()
            ->first();

        if ($inv) {
            $inv->increment('stock', $quantity);
        }
    }
}
