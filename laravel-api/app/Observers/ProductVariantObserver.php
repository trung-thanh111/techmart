<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Services\Impl\Inventory\InventorySyncService;
use Illuminate\Support\Facades\Log;

class ProductVariantObserver
{
    protected $inventorySyncService;

    public function __construct(InventorySyncService $inventorySyncService)
    {
        $this->inventorySyncService = $inventorySyncService;
    }

    /**
     * Handle the ProductVariant "created" event.
     * Tự động tạo inventory records cho tất cả agencies
     */
    public function created(ProductVariant $productVariant): void
    {
        try {
            $this->inventorySyncService->syncInventoryForVariant($productVariant);
        } catch (\Exception $e) {
            Log::error('Failed to sync inventory for variant: ' . $productVariant->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Không throw exception để không block việc tạo variant
            // Có thể retry sau hoặc log để admin biết
        }
    }

    /**
     * Handle the ProductVariant "updated" event.
     * Cập nhật price_override trong inventory nếu price thay đổi
     */
    public function updated(ProductVariant $productVariant): void
    {
        try {
            // Chỉ update nếu price thay đổi
            if ($productVariant->wasChanged('price')) {
                $this->inventorySyncService->updatePriceOverrideForVariant($productVariant);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update inventory price for variant: ' . $productVariant->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the ProductVariant "deleting" event.
     * Xóa inventory records liên quan
     *
     * Note: Không dùng deleted vì muốn xóa trước khi variant bị xóa
     */
    public function deleting(ProductVariant $productVariant): void
    {
        try {
            $this->inventorySyncService->deleteInventoryForVariant($productVariant);
        } catch (\Exception $e) {
            Log::error('Failed to delete inventory for variant: ' . $productVariant->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
