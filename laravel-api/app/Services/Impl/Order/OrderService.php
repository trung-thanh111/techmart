<?php

namespace App\Services\Impl\Order;

use App\Enums\Common;
use App\Enums\OrderEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\SecurityException;
use App\Repositories\Order\OrderRepository;
use App\Http\Resources\Order\OrderResource;
use App\Services\Interfaces\Order\OrderServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class OrderService extends BaseService implements OrderServiceInterface
{
    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['status', 'payment_status', 'user_id'];
    protected $fieldSearchs = ['code', 'customer_name', 'customer_email', 'customer_phone'];

    protected $with = ['order_items', 'order_status_histories', 'order_shipments', 'payment_transactions', 'order_refunds', 'user'];

    protected const STATUS_PRIORITY = [
        'pending' => 0,
        'confirmed' => 1,
        'processing' => 2,
        'shipped' => 3,
        'delivered' => 4,
        'completed' => 5, // if exists
        'cancelled' => -1, // Special case
        'refunded' => -1,  // Special case
        'failed' => -1,    // Special case
    ];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'orders';
    protected $cacheTags = ['orders:collection'];

    protected $paramField = 'code';

    protected $useUpload = false;

    protected float $taxRate = 0.1; // 10%

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * Tạo đơn hàng mới với items
     * Flow: validate stock -> reserve stock -> create order -> create items -> create history
     */
    public function createOrder(Request $request)
    {
        $order = DB::transaction(function () use ($request) {
            $items = $request->input('items', []);
            $userId = null;
            try {
                if ($user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate()) {
                    $userId = $user->id;
                }
            } catch (\Throwable $e) {
                if (auth(Common::API)->check()) {
                    $userId = auth(Common::API)->id();
                }
            }

            // 1. Validate và reserve stock với pessimistic locking
            $reservedItems = $this->reserveStock($items);

            // 2. Calculate totals
            $totals = $this->calculateTotals($reservedItems, $request);

            // 3. Generate order code
            $orderCode = $this->generateOrderCode();

            // 4. Create order
            $orderData = array_merge(
                $request->only([
                    'customer_email',
                    'customer_phone',
                    'customer_name',
                    'payment_method',
                    'shipping_method',
                    'shipping_provider',
                    'shipping_address',
                    'shipping_city',
                    'shipping_ward',
                    'shipping_street',
                    'shipping_country_code',
                    'shipping_postal_code',
                    'customer_note',
                ]),
                [
                    'code' => $orderCode,
                    'user_id' => $userId,
                    'subtotal' => $totals['subtotal'],
                    'tax_amount' => $totals['tax_amount'],
                    'shipping_fee' => $totals['shipping_fee'],
                    'discount_amount' => $totals['discount_amount'],
                    'total_amount' => $totals['total_amount'],
                    'status' => OrderEnum::STATUS_PENDING,
                    'payment_status' => OrderEnum::PAYMENT_STATUS_UNPAID,
                ]
            );

            $order = Order::create($orderData);

            // 5. Create order items
            $this->createOrderItems($order, $reservedItems);

            // 6. Create initial status history
            $this->createStatusHistory($order, null, OrderEnum::STATUS_PENDING, $userId, 'customer', 'Đơn hàng được tạo');

            // 7. Clear cache
            $this->clearCacheByTags();

            // 8. Load relationships và return
            $order->load($this->with);

            return $order;
        });

        // 9. Gửi email xác nhận đặt hàng qua Queue
        if (!empty($order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->queue(new \App\Mail\OrderConfirmationMail($order));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not queue order confirmation email for order #{$order->code}: " . $e->getMessage());
            }
        }

        return $order;
    }

    /**
     * Reserve stock với pessimistic locking để handle race condition
     * Sử dụng SELECT FOR UPDATE để lock rows khi nhiều người mua cùng lúc
     */
    protected function reserveStock(array $items): array
    {
        $reservedItems = [];

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $variantId = $item['product_variant_id'] ?? null;
            $quantity = $item['quantity'];

            // Lock variant/product row để tránh race condition
            if ($variantId) {
                $variant = ProductVariant::lockForUpdate()->find($variantId);

                if (!$variant) {
                    throw new \Exception("Sản phẩm không tồn tại: variant_id = {$variantId}");
                }

                if ($variant->stock < $quantity) {
                    throw new \Exception(
                        "Sản phẩm '{$variant->name}' chỉ còn {$variant->stock} sản phẩm, không đủ {$quantity} sản phẩm yêu cầu"
                    );
                }

                // Trừ stock (pessimistic lock đảm bảo atomic)
                $variant->decrement('stock', $quantity);

                $product = Product::find($productId);

                $reservedItems[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'product_sku' => $variant->sku ?? $product->sku,
                    'product_name' => $product->name . ' - ' . $variant->name,
                    'product_description' => $product->description,
                    'product_attributes' => $variant->attribute_data,
                    'product_image' => $this->getProductImage($product->album),
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'original_price' => $variant->price,
                    'warranty_months' => $product->warranty ?? 0,
                ];
            } else {
                // Product không có variant được chọn -> lấy variant đầu tiên
                $product = Product::find($productId);

                if (!$product) {
                    throw new \Exception("Sản phẩm không tồn tại: product_id = {$productId}");
                }

                // Lấy variant đầu tiên của product để quản lý stock
                $variant = ProductVariant::lockForUpdate()
                    ->where('product_id', $productId)
                    ->where('publish', 1)
                    ->first();

                if (!$variant) {
                    throw new \Exception("Sản phẩm '{$product->name}' không có phiên bản nào khả dụng");
                }

                if ($variant->stock < $quantity) {
                    throw new \Exception(
                        "Sản phẩm '{$product->name}' chỉ còn {$variant->stock} sản phẩm, không đủ {$quantity} sản phẩm yêu cầu"
                    );
                }

                // Trừ stock từ variant đầu tiên
                $variant->decrement('stock', $quantity);

                $reservedItems[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $variant->id ?? null,
                    'product_sku' => $variant->sku ?? $product->sku,
                    'product_name' => $product->name . ' - ' . $variant->name,
                    'product_description' => $product->description,
                    'product_attributes' => $variant->attribute_data ?? null,
                    'product_image' => $this->getProductImage($product->album),
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'original_price' => $variant->price,
                    'warranty_months' => $product->warranty ?? 0,
                ];
            }
        }

        return $reservedItems;
    }

    /**
     * Rollback stock khi cancel order
     */
    protected function releaseStock(Order $order): void
    {
        foreach ($order->order_items as $item) {
            if ($item->product_variant_id) {
                ProductVariant::where('id', $item->product_variant_id)
                    ->increment('stock', $item->quantity);
            }
        }
    }

    /**
     * Calculate order totals
     */
    protected function calculateTotals(array $items, Request $request): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }

        // Calculate shipping fee logic
        // Use constants from OrderEnum if shipping_method is provided
        $shippingMethod = $request->input('shipping_method', OrderEnum::SHIPPING_METHOD_STANDARD);
        $shippingFee = $request->input('shipping_fee');

        if ($shippingFee === null) {
            // Auto calculate based on constant if not provided by FE (though FE usually sends it)
            // But good to have fallback/validation
            $shippingFee = OrderEnum::SHIPPING_FEES[$shippingMethod] ?? OrderEnum::SHIPPING_FEE_STANDARD;
        }

        $discountAmount = $request->input('discount_amount', 0);
        $taxAmount = $subtotal * OrderEnum::TAX_RATE; // Use constant
        $totalAmount = $subtotal + $taxAmount + $shippingFee - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount < 0 ? 0 : $totalAmount,
        ];
    }

    /**
     * Create order items từ reserved items
     */
    protected function createOrderItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = 0; // TODO: implement item-level discount
            $taxAmount = $subtotal * $this->taxRate;
            $total = $subtotal - $discountAmount + $taxAmount;

            $warrantyExpiresAt = null;
            if ($item['warranty_months'] > 0) {
                $warrantyExpiresAt = now()->addMonths($item['warranty_months']);
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'product_sku' => $item['product_sku'],
                'product_name' => $item['product_name'],
                'product_description' => $item['product_description'],
                'product_attributes' => $item['product_attributes'],
                'product_image' => $item['product_image'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'original_price' => $item['original_price'],
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
                'warranty_months' => $item['warranty_months'],
                'warranty_expires_at' => $warrantyExpiresAt,
                'status' => 'pending',
                'is_returnable' => true,
                'return_day' => 7,
            ]);
        }
    }

    /**
     * Generate unique order code
     */
    protected function generateOrderCode(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        $code = "{$prefix}{$timestamp}{$random}";

        // Ensure uniqueness
        while (Order::where('code', $code)->exists()) {
            $random = strtoupper(Str::random(4));
            $code = "{$prefix}{$timestamp}{$random}";
        }

        return $code;
    }

    /**
     * Update order status với history tracking
     */
    public function updateStatus(int $orderId, string $newStatus, ?string $note = null): Order
    {
        return DB::transaction(function () use ($orderId, $newStatus, $note) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $oldStatus = $order->status;

            if ($oldStatus === $newStatus) {
                return $order;
            }

            // Validate status transition
            $this->validateStatusTransition($oldStatus, $newStatus);

            // Update order status
            $order->status = $newStatus;

            // Update timestamps based on status
            switch ($newStatus) {
                case 'confirmed':
                    $order->confirmed_at = now();
                    break;
                case 'shipped':
                    $order->shipped_at = now();
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    break;
                case 'cancelled':
                    $order->cancelled_at = now();
                    // Release stock when cancelled
                    $this->releaseStock($order);
                    break;
                case 'refunded':
                    $order->refunded_at = now();
                    // Release stock when refunded
                    $this->releaseStock($order);
                    break;
            }

            $order->save();

            // Create status history
            $userId = auth(Common::API)->id();
            $userType = auth(Common::ADMIN)->check() ? 'admin' : 'customer';

            $this->createStatusHistory($order, $oldStatus, $newStatus, $userId, $userType, $note);

            // Clear cache
            $this->clearCacheByTags();

            // Sync items to order status (Order -> Items)
            $this->syncItemsToOrder($order, $newStatus);

            return $order->load($this->with);
        });
    }

    public function updateItemStatus(int $orderId, int $itemId, string $newStatus): Order
    {
        return DB::transaction(function () use ($orderId, $itemId, $newStatus) {
            $order = Order::findOrFail($orderId);
            $item = OrderItem::where('order_id', $orderId)->where('id', $itemId)->firstOrFail();

            if ($item->status === $newStatus) {
                return $order;
            }

            $allowedStatuses = ['pending', 'confirmed', 'processing'];
            if (!in_array($newStatus, $allowedStatuses)) {
                throw new \Exception("Chỉ được phép cập nhật trạng thái item sang: pending, confirmed, processing.");
            }

            $item->status = $newStatus;
            $item->save();

            $this->syncOrderToItems($order);

            $this->clearCacheByTags();

            return $order->load($this->with);
        });
    }

    protected function syncItemsToOrder(Order $order, string $newOrderStatus): void
    {
        $propagatingStatuses = ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        if (!in_array($newOrderStatus, $propagatingStatuses)) {
            return;
        }

        $orderPriority = self::STATUS_PRIORITY[$newOrderStatus] ?? -99;

        foreach ($order->order_items as $item) {
            $itemPriority = self::STATUS_PRIORITY[$item->status] ?? -99;
            if ($newOrderStatus === 'cancelled' || $newOrderStatus === 'refunded') {
                $item->status = $newOrderStatus;
                $item->save();
                continue;
            }
            if ($itemPriority < $orderPriority && $itemPriority >= 0) {
                $item->status = $newOrderStatus;
                $item->save();
            }
        }
    }

    protected function syncOrderToItems(Order $order): void
    {
        $items = $order->order_items()->get();
        if ($items->isEmpty()) return;

        $currentOrderStatus = $order->status;
        $currentOrderPriority = self::STATUS_PRIORITY[$currentOrderStatus] ?? -99;

        $minPriority = 999;
        $minStatus = null;

        foreach ($items as $item) {
            $p = self::STATUS_PRIORITY[$item->status] ?? 0;
            if ($p < 0) continue;

            if ($p < $minPriority) {
                $minPriority = $p;
                $minStatus = $item->status;
            }
        }

        if ($minStatus && $minPriority > $currentOrderPriority) {
            $this->updateStatus($order->id, $minStatus, "Tự động cập nhật theo trạng thái sản phẩm");
        }
    }

    /**
     * Validate status transition với permission checks
     * - Customer: chỉ cancel được ở status 'pending'
     * - Admin: cancel được đến 'processing', refund cần admin
     */
    protected function validateStatusTransition(string $from, string $to): void
    {
        $isAdmin = auth(Common::ADMIN)->check();

        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'failed'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
            'failed' => ['pending'], // Retry
        ];

        if (!isset($allowedTransitions[$from]) || !in_array($to, $allowedTransitions[$from])) {
            throw new \Exception("Không thể chuyển trạng thái từ '{$from}' sang '{$to}'");
        }

        // Permission checks
        if ($to === 'cancelled') {
            if (!$isAdmin && $from !== 'pending') {
                throw new \Exception("Bạn chỉ có thể hủy đơn hàng ở trạng thái chờ xác nhận");
            }
            if (!$isAdmin && in_array($from, ['confirmed', 'processing'])) {
                throw new \Exception("Chỉ admin mới có quyền hủy đơn hàng đã xác nhận/đang xử lý");
            }
        }

        if ($to === 'refunded') {
            if (!$isAdmin) {
                throw new \Exception("Chỉ admin mới có quyền hoàn tiền đơn hàng");
            }
        }
    }



    /**
     * Helper to get product image from album
     */
    protected function getProductImage($album): ?string
    {
        if (empty($album)) {
            return null;
        }

        $image = $album[0] ?? null;

        if (is_array($image)) {
            return $image['path'] ?? $image['url'] ?? null;
        }

        return is_string($image) ? $image : null;
    }

    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId, ?string $reason = null): Order
    {
        return $this->updateStatus($orderId, 'cancelled', $reason ?? 'Đơn hàng bị hủy');
    }

    /**
     * Create status history record
     */
    protected function createStatusHistory(
        Order $order,
        ?string $oldStatus,
        string $newStatus,
        ?int $userId,
        string $userType,
        ?string $note = null
    ): void {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $userId,
            'user_type' => $userType,
            'note' => $note,
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): Order
    {
        return DB::transaction(function () use ($orderId, $paymentStatus) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $order->payment_status = $paymentStatus;

            if ($paymentStatus === 'paid') {
                $order->paid_at = now();
            }

            $order->save();

            $this->clearCacheByTags();

            return $order->load($this->with);
        });
    }

    /**
     * Update refund status
     */
    public function updateRefundStatus(int $orderId, int $refundId, string $status): Order
    {
        return DB::transaction(function () use ($orderId, $refundId, $status) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Find refund item within the order
            $refund = $order->order_refunds()->where('id', $refundId)->firstOrFail();

            $refund->status = $status;

            // If completed, maybe trigger some side effects? 
            // For now just update status
            if ($status === 'approved') {
                $refund->approved_at = now();
                $refund->approved_by = auth(Common::API)->id();
            }

            $refund->save();

            $this->clearCacheByTags();

            return $order->load($this->with);
        });
    }

    /**
     * Override save để dùng createOrder cho store
     */
    public function save(Request $request, ?int $id = null)
    {
        if ($id === null) {
            // Create new order
            $order = $this->createOrder($request);
            return $this->getResource($order);
        }

        // Update existing order (basic fields only)
        return parent::save($request, $id);
    }

    protected function prepareModelData(Request $request): self
    {
        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new OrderResource($resource);
    }

    /**
     * Cập nhật ghi chú cho đơn hàng
     *
     * @param int $orderId
     * @param string|null $note
     * @param int|null $userId
     * @param string|null $adminNote
     * @return Order
     * @throws ModelNotFoundException
     * @throws SecurityException
     * @throws \InvalidArgumentException
     */
    public function updateCustomerNote(int $orderId, ?string $note, ?int $userId = null, ?string $adminNote = null): Order
    {
        $order = $this->repository->findById($orderId);
        if (!$order) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }

        // Kiểm tra quyền sở hữu nếu là client user
        if ($userId !== null && (int) $order->user_id !== (int) $userId) {
            throw new SecurityException(Lang::get('message.permission_denied'));
        }

        // Khách hàng chỉ được sửa ghi chú khi đơn hàng đang chờ xác nhận (pending)
        if ($userId !== null && $order->status !== OrderEnum::STATUS_PENDING) {
            throw new \InvalidArgumentException(Lang::get('message.order_cannot_update_note'));
        }

        if ($userId !== null || $note !== null) {
            $order->customer_note = $note;
        }

        if ($adminNote !== null) {
            $order->admin_note = $adminNote;
        }

        $order->save();

        $this->clearCacheById($orderId);
        $this->clearCacheByTags();

        return $order->load($this->with);
    }

    /**
     * Chuẩn hóa số điện thoại về định dạng chuẩn (ví dụ: 0987654321)
     */
    protected function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Loại bỏ khoảng trắng, dấu ngoặc, dấu gạch ngang, dấu chấm
        $cleaned = preg_replace('/[^0-9+]/', '', trim($phone));

        if (str_starts_with($cleaned, '+84')) {
            $cleaned = '0' . substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '84') && strlen($cleaned) >= 11) {
            $cleaned = '0' . substr($cleaned, 2);
        }

        return $cleaned;
    }

    /**
     * Tra cứu đơn hàng nhanh theo mã đơn và thông tin xác thực
     *
     * @param string $code
     * @param string|null $phone
     * @param string|null $email
     * @param int|null $userId
     * @param bool $isAdmin
     * @return Order
     * @throws ModelNotFoundException
     */
    public function trackOrder(string $code, ?string $phone = null, ?string $email = null, ?int $userId = null, bool $isAdmin = false): Order
    {
        $code = trim($code);
        $order = $this->repository->findByField('code', $code, $this->with);

        if (!$order) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }

        // Admin có toàn quyền tra cứu bất kỳ đơn nào
        if ($isAdmin) {
            return $order;
        }

        // User đã đăng nhập tra cứu đúng đơn của chính mình
        if ($userId !== null && (int) $order->user_id === (int) $userId) {
            return $order;
        }

        $matched = false;

        // Khớp số điện thoại
        if (!empty($phone)) {
            $normInputPhone = $this->normalizePhone($phone);
            $normOrderPhone = $this->normalizePhone($order->customer_phone);
            if ($normInputPhone && $normOrderPhone && $normInputPhone === $normOrderPhone) {
                $matched = true;
            }
        }

        // Khớp email
        if (!$matched && !empty($email)) {
            $normInputEmail = strtolower(trim($email));
            $normOrderEmail = strtolower(trim((string) $order->customer_email));
            if ($normInputEmail && $normOrderEmail && $normInputEmail === $normOrderEmail) {
                $matched = true;
            }
        }

        if (!$matched) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }

        return $order;
    }
}

