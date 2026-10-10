<?php

namespace Tests\Feature;

use App\Enums\Common;
use App\Enums\OrderEnum;
use App\Enums\PaymentEnum;
use App\Models\Agency;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Interfaces\Order\OrderServiceInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderInventoryAutoSyncTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $clientUser;
    protected Agency $agency;
    protected Product $product;
    protected ProductVariant $variant;
    protected Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Tạo Admin & Client
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_inventory_test@example.com'],
            ['name' => 'Admin Inventory Tester', 'password' => Hash::make('password123'), 'publish' => 1]
        );

        $this->clientUser = User::firstOrCreate(
            ['email' => 'client_inventory_test@example.com'],
            ['name' => 'Client Inventory Tester', 'password' => Hash::make('password123'), 'publish' => 1]
        );

        // 2. Tạo Agency
        $this->agency = Agency::firstOrCreate(
            ['slug' => 'agency-test-01'],
            ['name' => 'Agency Test Kho 1', 'publish' => 2]
        );

        // 3. Tạo Product
        $this->product = Product::firstOrCreate(
            ['slug' => 'test-inventory-sync-product'],
            [
                'name' => 'Test Inventory Sync Product',
                'sku' => 'PROD-INV-TEST',
                'price_origin' => 200000,
                'publish' => 2,
            ]
        );

        // 4. Tạo ProductVariant
        $this->variant = ProductVariant::firstOrCreate(
            ['sku' => 'VAR-INV-TEST-01'],
            [
                'product_id' => $this->product->id,
                'name' => 'Variant Inventory Test',
                'price' => 200000,
                'stock' => 50,
                'publish' => 1,
            ]
        );
        $this->variant->update(['stock' => 50]);

        // 5. Tạo Inventory tại Agency
        $this->inventory = Inventory::firstOrCreate(
            [
                'product_variant_id' => $this->variant->id,
                'agency_id' => $this->agency->id,
            ],
            [
                'stock' => 50,
                'publish' => 2,
            ]
        );
        $this->inventory->update(['stock' => 50]);

        // Đảm bảo môi trường test chỉ có inventory này của variant
        Inventory::where('product_variant_id', $this->variant->id)
            ->where('id', '!=', $this->inventory->id)
            ->delete();
    }

    /**
     * Tạo helper Order kèm OrderItem
     */
    protected function createTestOrder(int $quantity = 2, string $status = 'pending', string $paymentStatus = 'unpaid'): Order
    {
        $order = Order::create([
            'code' => 'ORD' . rand(10000000, 99999999),
            'user_id' => $this->clientUser->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer_inv_test@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Trai, HN',
            'subtotal' => 200000 * $quantity,
            'total_amount' => 200000 * $quantity,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => 'cod',
            'is_stock_deducted' => false,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'product_sku' => $this->variant->sku,
            'product_name' => $this->product->name,
            'quantity' => $quantity,
            'unit_price' => $this->variant->price,
            'subtotal' => $this->variant->price * $quantity,
            'total' => $this->variant->price * $quantity,
        ]);

        return $order->fresh();
    }

    /**
     * 1. Đơn COD: Chưa trừ kho khi pending, tự động trừ cả variant và inventory khi xác nhận (confirmed)
     */
    public function test_cod_order_creation_does_not_deduct_stock_until_confirmed(): void
    {
        $order = $this->createTestOrder(3, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);

        // Ban đầu chưa trừ kho
        $this->assertFalse((bool) $order->is_stock_deducted);
        $this->assertEquals(50, $this->variant->fresh()->stock);
        $this->assertEquals(50, $this->inventory->fresh()->stock);

        // Admin xác nhận đơn hàng
        $orderService = app(OrderServiceInterface::class);
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);

        // Sau khi xác nhận: Đã trừ kho cả 2 bảng
        $this->assertTrue((bool) $order->fresh()->is_stock_deducted);
        $this->assertEquals(47, $this->variant->fresh()->stock);
        $this->assertEquals(47, $this->inventory->fresh()->stock);
    }

    /**
     * 2. Thanh toán thành công (paid): Tự động trừ kho cả variant và inventory
     */
    public function test_payment_status_paid_automatically_deducts_stock(): void
    {
        $order = $this->createTestOrder(5, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);

        $orderService = app(OrderServiceInterface::class);
        $orderService->updatePaymentStatus($order->id, OrderEnum::PAYMENT_STATUS_PAID);

        $this->assertTrue((bool) $order->fresh()->is_stock_deducted);
        $this->assertEquals(45, $this->variant->fresh()->stock);
        $this->assertEquals(45, $this->inventory->fresh()->stock);
    }

    /**
     * 3. Idempotency: Xác nhận đơn rồi thanh toán -> không trừ 2 lần
     */
    public function test_stock_deduction_is_idempotent_when_confirmed_then_paid(): void
    {
        auth(Common::ADMIN)->setUser($this->adminUser);

        $order = $this->createTestOrder(4, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Lần 1: Xác nhận đơn -> trừ 4 (còn 46)
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);
        $this->assertEquals(46, $this->variant->fresh()->stock);
        $this->assertEquals(46, $this->inventory->fresh()->stock);

        // Lần 2: Thanh toán thành công -> không được trừ thêm
        $orderService->updatePaymentStatus($order->id, OrderEnum::PAYMENT_STATUS_PAID);
        $this->assertEquals(46, $this->variant->fresh()->stock);
        $this->assertEquals(46, $this->inventory->fresh()->stock);

        // Lần 3: Chuyển sang processing -> không được trừ thêm
        $orderService->updateStatus($order->id, OrderEnum::STATUS_PROCESSING);
        $this->assertEquals(46, $this->variant->fresh()->stock);
        $this->assertEquals(46, $this->inventory->fresh()->stock);
    }

    /**
     * 4. Hủy đơn hàng (cancelled): Hoàn lại kho cho cả 2 bảng
     */
    public function test_cancelled_order_restores_stock_to_both_tables(): void
    {
        auth(Common::ADMIN)->setUser($this->adminUser);

        $order = $this->createTestOrder(2, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Xác nhận đơn: trừ 2 (còn 48)
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);
        $this->assertEquals(48, $this->variant->fresh()->stock);
        $this->assertEquals(48, $this->inventory->fresh()->stock);

        // Admin hủy đơn: hoàn lại 2 (trở về 50)
        $orderService->cancelOrder($order->id, 'Khách hàng đổi ý');
        $this->assertFalse((bool) $order->fresh()->is_stock_deducted);
        $this->assertEquals(50, $this->variant->fresh()->stock);
        $this->assertEquals(50, $this->inventory->fresh()->stock);
    }

    /**
     * 5. Hoàn tiền đơn hàng (refunded): Hoàn lại kho cho cả 2 bảng
     */
    public function test_refunded_order_restores_stock_to_both_tables(): void
    {
        auth(Common::ADMIN)->setUser($this->adminUser);

        $order = $this->createTestOrder(6, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Luồng: confirmed -> processing -> shipped -> delivered
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);
        $orderService->updateStatus($order->id, OrderEnum::STATUS_PROCESSING);
        $orderService->updateStatus($order->id, OrderEnum::STATUS_SHIPPED);
        $orderService->updateStatus($order->id, OrderEnum::STATUS_DELIVERED);

        // Stock lúc này đã bị trừ 6 (còn 44)
        $this->assertEquals(44, $this->variant->fresh()->stock);
        $this->assertEquals(44, $this->inventory->fresh()->stock);

        // Hoàn tiền: hoàn lại 6 (trở về 50)
        $orderService->updateStatus($order->id, OrderEnum::STATUS_REFUNDED, 'Lỗi sản phẩm hoàn tiền');
        $this->assertFalse((bool) $order->fresh()->is_stock_deducted);
        $this->assertEquals(50, $this->variant->fresh()->stock);
        $this->assertEquals(50, $this->inventory->fresh()->stock);
    }

    /**
     * 6. Đơn chưa từng xác nhận mà bị hủy: Không được cộng tồn kho ảo
     */
    public function test_unconfirmed_order_cancellation_does_not_inflate_stock(): void
    {
        $order = $this->createTestOrder(3, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Khách hủy ngay khi đang pending (chưa từng trừ kho)
        $orderService->cancelOrder($order->id, 'Hủy ngay lập tức');

        $this->assertFalse((bool) $order->fresh()->is_stock_deducted);
        // Stock vẫn giữ nguyên 50, không bị tăng thành 53
        $this->assertEquals(50, $this->variant->fresh()->stock);
        $this->assertEquals(50, $this->inventory->fresh()->stock);
    }

    /**
     * 7. Hủy đơn qua direct update / idempotency: Không hoàn kho 2 lần
     */
    public function test_double_cancellation_or_refund_is_idempotent(): void
    {
        auth(Common::ADMIN)->setUser($this->adminUser);

        $order = $this->createTestOrder(4, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Xác nhận đơn: trừ 4 (còn 46)
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);
        $this->assertEquals(46, $this->variant->fresh()->stock);

        // Hủy đơn lần 1: hoàn 4 (trở về 50)
        $orderService->cancelOrder($order->id, 'Hủy đơn');
        $this->assertEquals(50, $this->variant->fresh()->stock);

        // Gọi releaseStock trực tiếp lại: idempotent, không được hoàn thêm
        $orderService->releaseStock($order->fresh());
        $this->assertEquals(50, $this->variant->fresh()->stock);
        $this->assertEquals(50, $this->inventory->fresh()->stock);
    }

    /**
     * 8. Phân bổ tồn kho khi variant nằm ở nhiều Agency
     */
    public function test_multi_agency_inventory_deduction_distribution(): void
    {
        auth(Common::ADMIN)->setUser($this->adminUser);
        // Tạo thêm Agency 2
        $agency2 = Agency::firstOrCreate(
            ['slug' => 'agency-test-02'],
            ['name' => 'Agency Test Kho 2', 'publish' => 2]
        );

        // Agency 1 có 5 sp, Agency 2 có 10 sp
        $this->inventory->update(['stock' => 5]);
        $inv2 = Inventory::firstOrCreate(
            [
                'product_variant_id' => $this->variant->id,
                'agency_id' => $agency2->id,
            ],
            [
                'stock' => 10,
                'publish' => 2,
            ]
        );
        $inv2->update(['stock' => 10]);
        $this->variant->update(['stock' => 15]);

        // Đơn hàng mua 8 sản phẩm (ưu tiên trừ kho Agency 2 có 10 sp trước)
        $order = $this->createTestOrder(8, OrderEnum::STATUS_PENDING, OrderEnum::PAYMENT_STATUS_UNPAID);
        $orderService = app(OrderServiceInterface::class);

        // Xác nhận đơn
        $orderService->updateStatus($order->id, OrderEnum::STATUS_CONFIRMED);

        // Variant còn 15 - 8 = 7
        $this->assertEquals(7, $this->variant->fresh()->stock);
        // Agency 2 từ 10 trừ 8 còn 2, Agency 1 giữ nguyên 5 (tổng 2 + 5 = 7)
        $this->assertEquals(2, $inv2->fresh()->stock);
        $this->assertEquals(5, $this->inventory->fresh()->stock);

        // Khi hủy đơn: hoàn lại đúng 8 sp
        $orderService->cancelOrder($order->id, 'Hủy đơn');
        $this->assertEquals(15, $this->variant->fresh()->stock);
        $this->assertEquals(15, $this->inventory->fresh()->stock + $inv2->fresh()->stock);
    }
}
