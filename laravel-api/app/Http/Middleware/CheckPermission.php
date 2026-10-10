<?php

namespace App\Http\Middleware;

use App\Enums\Common;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Models\Permission;
use App\Services\Impl\Permission\PermissionService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    private $permissionService;

    /**
     * Map controller methods to canonical permission actions
     */
    private const METHOD_ACTION_MAP = [
        'index' => 'paginate',
        'paginate' => 'paginate',
        'destroy' => 'delete',
        'delete' => 'delete',
        'store' => 'store',
        'show' => 'show',
        'update' => 'update',
        'bulkStore' => 'bulkStore',
        'bulkUpdate' => 'bulkUpdate',
        'bulkDelete' => 'bulkDelete',
        'attach' => 'attach',
        'detach' => 'detach',
        'restore' => 'restore',
        // Custom methods mapped to primary permission categories
        'updateStatus' => 'update',
        'bulkStatus' => 'bulkUpdate',
        'updatePaymentStatus' => 'update',
        'updateItemStatus' => 'update',
        'updateRefundStatus' => 'update',
        'rebuildTree' => 'update',
        'dragDrop' => 'update',
        'getPermissions' => 'show',
        'statistics' => 'paginate',
        'charts' => 'paginate',
        'recentOrders' => 'paginate',
        'topProducts' => 'paginate',
        'saveSettings' => 'update',
        'structure' => 'show',
    ];

    /**
     * Custom controller class to permission module mappings
     */
    private const CONTROLLER_MODULE_MAP = [
        'TagController' => 'post_tags',
        'DashboardController' => 'dashboard',
        'ChangePasswordController' => 'users',
        'ImageController' => 'images',
        'MailController' => 'mails',
    ];

    public function __construct(
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Xác định User từ guard admin, api hoặc request
        $user = auth(Common::ADMIN)->user()
            ?: auth(Common::API)->user()
            ?: $request->user();

        if (!$user) {
            return ApiResource::message(Lang::get('message.user_undefined'), Response::HTTP_UNAUTHORIZED);
        }

        // 2. Lấy controller và method từ route
        $routeAction = $request->route()?->getActionName();
        if (!$routeAction || !str_contains($routeAction, '@')) {
            return $next($request);
        }

        [$controllerClass, $method] = explode('@', $routeAction);
        $controllerBase = class_basename($controllerClass);

        // Bỏ qua kiểm tra permission cho các tác vụ cá nhân của admin đã đăng nhập
        if (in_array($method, ['changePassword', 'uploadTemp'])) {
            return $next($request);
        }

        // 3. Tải danh sách catalogue/role của user
        $user->loadMissing(['user_catalogue.permissions', 'user_catalogues.permissions']);

        $catalogues = collect();
        if ($user->user_catalogue) {
            $catalogues->push($user->user_catalogue);
        }
        if ($user->user_catalogues && $user->user_catalogues->isNotEmpty()) {
            $catalogues = $catalogues->merge($user->user_catalogues);
        }

        // 4. Nếu là Super Admin (is_admin = true hoặc slug = 'admin' hoặc user_catalogue_id = 2) -> Full quyền
        $isSuperAdmin = (int) $user->user_catalogue_id === 2 || $catalogues->contains(function ($c) {
            return (bool) $c->is_admin || $c->slug === 'admin' || (int) $c->id === 2;
        });

        if ($isSuperAdmin) {
            return $next($request);
        }

        if ($catalogues->isEmpty()) {
            return ApiResource::message(Lang::get('message.user_permission_not_granted'), Response::HTTP_FORBIDDEN);
        }

        // 5. Xác định tên module
        if (isset(self::CONTROLLER_MODULE_MAP[$controllerBase])) {
            $moduleName = self::CONTROLLER_MODULE_MAP[$controllerBase];
        } else {
            $baseName = Str::replaceLast('Controller', '', $controllerBase);
            $moduleName = Str::plural(Str::snake($baseName));
        }

        // 6. Xác định tên action
        $actionName = self::METHOD_ACTION_MAP[$method] ?? $method;
        $permissionName = "{$moduleName}:{$actionName}";

        // 7. Tìm permission trong DB (hỗ trợ fallback nếu lưu theo actionName hoặc method)
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission && $actionName !== $method) {
            $permission = Permission::where('name', "{$moduleName}:{$method}")->first();
        }

        if (!$permission) {
            Log::warning("RBAC: Permission {$permissionName} not found in DB for user #{$user->id}");
            return ApiResource::message(Lang::get('message.access_denied'), Response::HTTP_FORBIDDEN);
        }

        $requiredVal = (int) $permission->value;

        // 8. Kiểm tra quyền bitwise qua các catalogues của user
        $hasPermission = false;
        foreach ($catalogues as $catalogue) {
            $modulePermissions = $catalogue->permissions
                ->where('module', $moduleName)
                ->pluck('value')
                ->toArray();

            $totalPermissions = array_reduce($modulePermissions, function ($carry, $item) {
                return $carry | (int) $item;
            }, 0);

            if (($totalPermissions & $requiredVal) === $requiredVal) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return ApiResource::message(Lang::get('message.access_denied'), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
