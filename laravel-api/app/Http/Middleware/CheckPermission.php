<?php
namespace App\Http\Middleware;

use App\Enums\Common;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Services\Impl\Permission\PermissionService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;



class CheckPermission
{

    private $permissionService;
    private $auth;

    public function __construct(
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
        $this->auth = auth(Common::API);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $action = $request->route()->getActionName();
            [$controller, $method] = explode('@', $action);
            $controllerName = str_replace('_controller', 's', Str::snake(class_basename($controller)));

            // Map method names để khớp với permission names trong database
            // index -> paginate (vì index gọi paginate trong BaseController)
            // destroy -> delete (vì destroy gọi delete trong service)
            $methodMap = [
                'index' => 'paginate',
                'destroy' => 'delete',
            ];
            $permissionMethod = $methodMap[$method] ?? $method;

            $permissionName = "{$controllerName}:{$permissionMethod}";
            if (!$permission = $this->permissionService->findByField('name', $permissionName)) {
                return ApiResource::message(Lang::get('message.access_denied'), Response::HTTP_FORBIDDEN);
            }
            $requiredval = $permission->value;
            /**
             * @var User user
             */
            $user = $this->auth->user();
            if (!$user) {
                return ApiResource::message(Lang::get('message.user_undefined'), Response::HTTP_UNAUTHORIZED);
            }
            $user->load(['user_catalogues.permissions']);
            if (!$user->user_catalogues || $user->user_catalogues->isEmpty()) {
                return ApiResource::message(Lang::get('message.user_permission_not_granted'), Response::HTTP_FORBIDDEN);
            }
            $hasPermission = false;
            foreach ($user->user_catalogues as $key => $val) {
                $permissions = $val->permissions->where('module', $controllerName)->pluck('value')->toArray();
                // tính tổng và cộng dồn quyền của ng dùng
                $totalPermissions = array_reduce($permissions, function ($carry, $item) {
                    return $carry | $item;
                }, 0);
                // dùng bitwise AND để kiểm tra quyền
                if (($totalPermissions & $requiredval) === $requiredval) {
                    $hasPermission = true;
                    break;
                }
            }
            if (!$hasPermission) {
                return ApiResource::message(Lang::get('message.access_denied'), Response::HTTP_FORBIDDEN);
            }
        } catch (\Throwable $th) {
            // Log error for debugging
            Log::error('CheckPermission middleware error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);
            return ApiResource::message(Lang::get('message.access_denied'), Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
