<?php

namespace App\Http\Controllers\Api\V1\Order;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Resources\ApiResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Order\Order\BulkStoreRequest;
use App\Http\Requests\Order\Order\BulkUpdateRequest;
use App\Http\Requests\Order\Order\StoreRequest;
use App\Http\Requests\Order\Order\UpdateRequest;
use App\Services\Interfaces\Order\OrderServiceInterface as OrderService;
use Illuminate\Support\Facades\Lang;

class OrderController extends BaseController
{
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        // Khi user đăng nhập bằng guard 'api' (khách hàng), tự động giới hạn chỉ lấy đơn hàng của chính mình
        if (auth('api')->check() && !auth('admin')->check()) {
            $request->merge(['user_id' => auth('api')->id()]);
        }

        return parent::index($request);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->baseSave($request);
    }

    public function bulkStore(BulkStoreRequest $request): JsonResponse
    {
        return $this->baseBulkSave($request, 'store');
    }

    public function bulkUpdate(BulkUpdateRequest $request): JsonResponse
    {
        return $this->baseBulkSave($request, 'update');
    }

    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        return $this->baseSave($request, $id);
    }

    /**
     * Update order status
     * POST /orders/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded,failed',
            'note' => 'nullable|string|max:500',
        ]);

        $order = $this->service->updateStatus(
            $id,
            $request->input('status'),
            $request->input('note')
        );

        return ApiResource::ok($order, Lang::get('message.update_data_successfully'));
    }

    /**
     * Cancel order
     * POST /orders/{id}/cancel
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order = $this->service->cancelOrder($id, $request->input('reason'));

        return ApiResource::ok($order, 'Đơn hàng đã được hủy thành công');
    }

    /**
     * Update payment status
     * POST /orders/{id}/payment-status
     */
    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'payment_status' => 'required|string|in:unpaid,pending,paid,partially_paid,refunded,failed',
        ]);

        $order = $this->service->updatePaymentStatus($id, $request->input('payment_status'));

        return ApiResource::ok($order, Lang::get('message.update_data_successfully'));
    }

    public function updateRefundStatus(Request $request, int $id, int $refundId): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,approved,processing,completed,rejected',
        ]);

        $order = $this->service->updateRefundStatus($id, $refundId, $request->input('status'));

        return ApiResource::ok($order, Lang::get('message.update_data_successfully'));
    }

    public function updateItemStatus(Request $request, int $id, int $itemId): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing',
        ]);

        $order = $this->service->updateItemStatus($id, $itemId, $request->input('status'));

        return ApiResource::ok($order, Lang::get('message.update_data_successfully'));
    }

    public function attach(RelationRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRelation($request, $id, 'attach');
    }

    public function detach(RelationRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRelation($request, $id, 'detach');
    }

    public function restore(RestoreRequest $request, int $id = 0): JsonResponse
    {
        return $this->baseRestore($request, $id);
    }

    protected function getService()
    {
        return $this->service;
    }
}
