<?php

namespace App\Observers;

use App\Models\Agency;
use App\Services\Impl\Inventory\InventorySyncService;
use Illuminate\Support\Facades\Log;

class AgencyObserver
{
    protected $inventorySyncService;

    public function __construct(InventorySyncService $inventorySyncService)
    {
        $this->inventorySyncService = $inventorySyncService;
    }

    /**
     * Handle the Agency "created" event.
     * Tự động tạo inventory records cho tất cả variants hiện có
     */
    public function created(Agency $agency): void
    {
        try {
            // Chỉ sync nếu agency được publish (active)
            if ($agency->publish == 2) {
                $this->inventorySyncService->syncInventoryForNewAgency($agency);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync inventory for new agency: ' . $agency->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Không throw exception để không block việc tạo agency
        }
    }

    /**
     * Handle the Agency "updated" event.
     * Sync inventory khi agency được publish (từ unpublish -> publish)
     */
    public function updated(Agency $agency): void
    {
        try {
            if ($agency->publish == 2 && $agency->wasChanged('publish')) {
                $this->inventorySyncService->syncInventoryForNewAgency($agency);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync inventory for updated agency: ' . $agency->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
