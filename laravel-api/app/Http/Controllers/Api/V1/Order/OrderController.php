<?php

namespace App\Http\Controllers\Api\V1\Order;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RestoreRequest;
use App\Http\Requests\RelationRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\Order\OrderResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Order\Order\BulkStoreRequest;
use App\Http\Requests\Order\Order\BulkUpdateRequest;
use App\Http\Requests\Order\Order\StoreRequest;
use App\Http\Requests\Order\Order\UpdateRequest;
use App\Services\Interfaces\Order\OrderServiceInterface as OrderService;
use App\Exceptions\SecurityException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
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
        $guard = $request->attributes->get('guard');
        if (!$guard) {
            try {
                $guard = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->getPayload()->get('guard');
            } catch (\Throwable $e) {}
        }

        // Khi user đăng nhập bằng guard 'api' (khách hàng), tự động giới hạn chỉ lấy đơn hàng của chính mình
        if ($guard === 'api' || ($guard === null && auth('api')->check() && !auth('admin')->check())) {
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
     * Update order note (customer_note / admin_note)
     * POST|PUT|PATCH /orders/{id}/note
     */
    public function updateNote(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'customer_note' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $customerNote = $request->input('customer_note', $request->input('note'));
        $adminNote = $request->input('admin_note');

        $guard = $request->attributes->get('guard');
        if (!$guard) {
            try {
                $guard = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->getPayload()->get('guard');
            } catch (\Throwable $e) {}
        }

        $userId = null;
        if ($guard === 'api' || ($guard === null && auth('api')->check() && !auth('admin')->check())) {
            $userId = auth('api')->id();
        }

        try {
            $order = $this->service->updateCustomerNote($id, $customerNote, $userId, $adminNote);
            return ApiResource::ok(new OrderResource($order), Lang::get('message.update_data_successfully'));
        } catch (SecurityException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\InvalidArgumentException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            return ApiResource::message($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Tra cứu nhanh đơn hàng (Public hoặc Authenticated)
     * GET|POST /orders/track
     */
    public function track(Request $request): JsonResponse
    {
        $guard = $request->attributes->get('guard');
        if (!$guard) {
            try {
                $guard = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->getPayload()->get('guard');
            } catch (\Throwable $e) {}
        }

        $isAdmin = ($guard === 'admin');
        $userId = null;
        if (!$isAdmin) {
            try {
                if ($user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate()) {
                    $userId = $user->id;
                }
            } catch (\Throwable $e) {
                if (auth('api')->check()) {
                    $userId = auth('api')->id();
                }
            }
        }

        $rules = [
            'code' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
        ];

        // Khách vãng lai bắt buộc phải nhập phone hoặc email
        if (!$isAdmin && $userId === null) {
            $rules['phone'] = 'required_without:email|nullable|string|max:30';
            $rules['email'] = 'required_without:phone|nullable|email|max:150';
        }

        $request->validate($rules);

        try {
            $order = $this->service->trackOrder(
                (string) $request->input('code'),
                $request->input('phone'),
                $request->input('email'),
                $userId,
                $isAdmin
            );

            return ApiResource::ok(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return ApiResource::message(Lang::get('message.record_not_found'), Response::HTTP_NOT_FOUND);
        }
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
