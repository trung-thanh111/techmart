<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ApiResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

/**
 * Trườu tượng hóa BaseController
 * để các lớp con phải tự triển khai để biết gọi vào service hợp lệ
 */
abstract class BaseController extends Controller
{
    protected $service;
    public function __construct()
    {
        $this->service = $this->getService();
    }

    // hàm này thông báo cho các lớp con triển khai để lấy đúng service
    protected abstract function getService();

    public function index(Request $request): JsonResponse
    {
        $response = $this->service->paginate($request);
        return ApiResource::ok($response, Lang::get('message.successfully'));
    }

    public function baseSave(Request $request, ?int $id = null): JsonResponse
    {
        $response = $this->service->save($request, $id);
        $res = ApiResource::ok($response, Lang::get('message.save_data_successfully'));
        return $this->attachCacheInvalidateHeader($res);
    }

    public function destroy(int $id = 0)
    {
        $response = $this->service->destroy($id);
        $res = ApiResource::message(Lang::get('message.delete_successfully'));
        return $this->attachCacheInvalidateHeader($res);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $countRecord = count($request->ids);
        $response = $this->service->bulkDestroy($request);
        $res = ApiResource::message(Lang::get("message.delete_successfully") . " ({$countRecord})");
        return $this->attachCacheInvalidateHeader($res);
    }

    protected function getCacheInvalidateDomains(): array
    {
        $class = class_basename(static::class);
        if (str_contains($class, 'ProductCatalogue')) return ['CATALOGUE', 'PRODUCT'];
        if (str_contains($class, 'Product')) return ['PRODUCT'];
        if (str_contains($class, 'PostCatalogue') || str_contains($class, 'Post')) return ['POST'];
        if (str_contains($class, 'Menu')) return ['MENU'];
        if (str_contains($class, 'Promotion')) return ['PROMOTION', 'PRODUCT'];
        if (str_contains($class, 'Agency')) return ['AGENCY'];
        if (str_contains($class, 'Faq')) return ['FAQ'];
        if (str_contains($class, 'Banner')) return ['BANNER'];
        if (str_contains($class, 'Order') || str_contains($class, 'Cart')) return ['USER', 'PRODUCT'];
        if (str_contains($class, 'System')) return ['SYSTEM'];
        return [];
    }

    protected function attachCacheInvalidateHeader(JsonResponse $response): JsonResponse
    {
        $domains = $this->getCacheInvalidateDomains();
        if (!empty($domains)) {
            $response->header('X-Cache-Invalidate', implode(',', $domains));
        }
        return $response;
    }

    public function show($id): JsonResponse
    {
        $response = $this->service->show($id);
        return ApiResource::ok($response, Lang::get('message.successfully'));
    }

    public function showByKeyword(string $keyword = ''): JsonResponse
    {
        $response = $this->service->showByKeyword($keyword);
        return ApiResource::ok($response, Lang::get('message.successfully'));
    }

    public function baseRelation(Request $request, int $id = 0, string $action = '')
    {
        $response = $this->service->relation($request, $id, $action);
        $res = ApiResource::message(Lang::get('message.action_relation_successfully'));
        return $this->attachCacheInvalidateHeader($res);
    }

    public function baseBulkSave(Request $request, string $action = ''): JsonResponse
    {
        $countRecord = count($request->data);
        $message = $action === 'store' ? Lang::get('message.save_data_successfully') . " ({$countRecord})" : Lang::get('message.update_data_successfully') . " ({$countRecord})";
        $response = $this->service->bulkSave($request, $action);
        $res = ApiResource::message($message);
        return $this->attachCacheInvalidateHeader($res);
    }

    public function baseRestore(request $request, int $id = 0): JsonResponse
    {
        $response = $this->service->restore($request, $id);
        $res = ApiResource::ok($response, Lang::get('message.restore_successfully') . " ({$id})");
        return $this->attachCacheInvalidateHeader($res);
    }
}
