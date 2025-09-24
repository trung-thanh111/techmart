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
        return ApiResource::ok($response, Lang::get('message.save_data_successfully'));
    }

    public function destroy(int $id = 0)
    {
        $response = $this->service->destroy($id);
        return ApiResource::message(Lang::get('message.delete_successfully'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $countRecord = count($request->ids);
        $response = $this->service->bulkDestroy($request);
        return ApiResource::message(Lang::get("message.delete_successfully")." ({$countRecord})");
    }

    public function show(int $id = 0): JsonResponse
    {
        $response = $this->service->show($id);
        return ApiResource::ok($response, Lang::get('message.successfully'));
    }
    
    public function showBySlug(string $slug = ''): JsonResponse
    {
        $response = $this->service->showBySlug($slug);
        return ApiResource::ok($response, Lang::get('message.successfully'));
    }

    public function baseRelation(Request $request, int $id = 0, string $action = '')
    {
        $response = $this->service->relation($request, $id, $action);
        return ApiResource::message(Lang::get('message.action_relation_successfully'));
    }

    public function baseBulkSave(Request $request, string $action = ''): JsonResponse
    {
        $countRecord = count($request->data);
        $message = $action === 'store' ? Lang::get('message.save_data_successfully')." ({$countRecord})" : Lang::get('message.update_data_successfully') ." ({$countRecord})";
        $response = $this->service->bulkSave($request, $action);
        return ApiResource::message($message);
    }

    public function baseRestore(request $request, int $id = 0): JsonResponse
    {
        $response = $this->service->restore($request, $id);
        return ApiResource::ok($response, Lang::get('message.restore_successfully')." ({$id})");
    }

}
