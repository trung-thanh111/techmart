<?php

namespace App\Observers;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PermissionObserver
{
    /**
     * Các actions cần generate permissions
     * Format: ['action' => 'value', ...]
     * Value là bitwise value cho permission
     */
    protected const ACTIONS = [
        'store' => 1,           // 2^0 = 1
        'bulkStore' => 2,       // 2^1 = 2
        'bulkUpdate' => 4,      // 2^2 = 4
        'show' => 8,            // 2^3 = 8
        'update' => 16,         // 2^4 = 16
        'delete' => 32,         // 2^5 = 32
        'restore' => 64,        // 2^6 = 64
        'bulkDelete' => 128,    // 2^7 = 128
        'attach' => 256,        // 2^8 = 256
        'detach' => 512,        // 2^9 = 512
        'paginate' => 1024,     // 2^10 = 1024
    ];

    protected const ACTION_TITLES = [
        'store' => 'Quyền thêm mới',
        'bulkStore' => 'Quyền thêm mới hàng loạt',
        'bulkUpdate' => 'Quyền cập nhật hàng loạt',
        'show' => 'Quyền xem chi tiết',
        'update' => 'Quyền cập nhật',
        'delete' => 'Quyền xóa',
        'restore' => 'Quyền khôi phục',
        'bulkDelete' => 'Quyền xóa hàng loạt',
        'attach' => 'Quyền gắn quan hệ',
        'detach' => 'Quyền gỡ quan hệ',
        'paginate' => 'Quyền xem danh sách',
    ];

    /**
     * Generate permissions cho một module
     *
     * @param string $moduleName Tên module (ví dụ: 'users', 'products')
     * @return array Danh sách permissions đã tạo
     */
    public function generateForModule(string $moduleName): array
    {
        $created = [];
        $moduleName = Str::lower(Str::snake($moduleName));

        try {
            DB::beginTransaction();

            foreach (self::ACTIONS as $action => $value) {
                $permissionName = "{$moduleName}:{$action}";

                $existing = Permission::where('name', $permissionName)->first();

                if ($existing) {
                    Log::info("Permission {$permissionName} already exists, skipping...");
                    continue;
                }

                $title = self::ACTION_TITLES[$action] ?? "Quyền {$action}";

                $permission = Permission::create([
                    'name' => $permissionName,
                    'module' => $moduleName,
                    'value' => $value,
                    'title' => $title,
                    'user_id' => null,
                    'publish' => 2,
                ]);

                $created[] = $permission;
                Log::info("Created permission: {$permissionName} with value {$value}");
            }

            DB::commit();
            return $created;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate permissions for module {$moduleName}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all available actions
     *
     * @return array
     */
    public static function getActions(): array
    {
        return self::ACTIONS;
    }

    /**
     * Get action title
     *
     * @param string $action
     * @return string
     */
    public static function getActionTitle(string $action): string
    {
        return self::ACTION_TITLES[$action] ?? "Quyền {$action}";
    }
}
