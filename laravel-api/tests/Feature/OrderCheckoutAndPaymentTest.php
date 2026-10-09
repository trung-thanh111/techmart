<?php

namespace Tests\Feature;

use App\Enums\OrderEnum;
use App\Enums\PaymentEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderCheckoutAndPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientUser = User::firstOrCreate(
            ['email' => 'checkout_test_user@example.com'],
            ['name' => 'Checkout Test User', 'password' => Hash::make('password123'), 'publish' => 1]
        );

        $this->product = Product::firstOrCreate(
            ['slug' => 'test-checkout-product'],
            [
                'name' => 'Test Checkout Product',
                'sku' => 'PROD-SKU-TEST',
                'price_origin' => 100000,
                'publish' => 2,
            ]
        );

        \App\Models\ProductVariant::firstOrCreate(
            ['sku' => 'TEST-CHECKOUT-SKU'],
            [
                'product_id' => $this->product->id,
                'name' => 'Default Variant',
                'price' => 100000,
                'stock' => 50,
                'publish' => 1,
            ]
        );
    }

    /**
     * Test: Đơn hàng tạo bởi user đã đăng nhập sẽ tự động gắn user_id
     */
    public function test_authenticated_user_order_creation_persists_user_id(): void
    {
        $token = JWTAuth::fromUser($this->clientUser);

        $payload = [
            'customer_name' => 'Checkout User',
            'customer_email' => $this->clientUser->email,
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Le Loi, Da Nang',
            'shipping_method' => 'standard',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/orders', $payload);

        $response->assertStatus(200);
        $orderData = $response->json('data');

        $this->assertNotNull($orderData['id'] ?? null);
        $order = Order::find($orderData['id']);
        $this->assertNotNull($order);
        $this->assertEquals($this->clientUser->id, $order->user_id);
    }

    /**
     * Test: Đơn hàng tạo bởi khách vãng lai lưu user_id = null an toàn
     */
    public function test_guest_user_order_creation_persists_null_user_id(): void
    {
        $payload = [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest_checkout@example.com',
            'customer_phone' => '0912345678',
            'shipping_address' => '456 Tran Phu, Da Nang',
            'shipping_method' => 'standard',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(200);
        $orderData = $response->json('data');

        $order = Order::find($orderData['id']);
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
    }

    /**
     * Test: Tạo payment link VNPay thành công cho đơn hàng
     */
    public function test_create_vnpay_payment_returns_pay_url(): void
    {
        $order = Order::create([
            'code' => 'ORD' . rand(10000000, 99999999),
            'user_id' => $this->clientUser->id,
            'customer_name' => 'VNPay Payer',
            'customer_email' => 'vnpay@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '789 Nguyen Thi Minh Khai',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => OrderEnum::STATUS_PENDING,
            'payment_status' => OrderEnum::PAYMENT_STATUS_UNPAID,
            'payment_method' => PaymentEnum::GATEWAY_VNPAY,
        ]);

        $response = $this->postJson('/api/v1/payment/create', [
            'order_id' => $order->id,
            'gateway' => PaymentEnum::GATEWAY_VNPAY,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'pay_url',
                    'request_id',
                ],
            ]);

        $this->assertStringContainsString('vnpayment.vn', $response->json('data.pay_url'));
    }

    /**
     * Test: VNPay return URL đồng bộ trạng thái đơn hàng sang paid và confirmed
     */
    public function test_vnpay_return_syncs_order_status_to_paid(): void
    {
        $order = Order::create([
            'code' => 'ORD' . rand(10000000, 99999999),
            'user_id' => $this->clientUser->id,
            'customer_name' => 'VNPay User',
            'customer_email' => 'vnpay_return@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Dien Bien Phu',
            'subtotal' => 300000,
            'total_amount' => 300000,
            'status' => OrderEnum::STATUS_PENDING,
            'payment_status' => OrderEnum::PAYMENT_STATUS_UNPAID,
            'payment_method' => PaymentEnum::GATEWAY_VNPAY,
        ]);

        // Tạo payment transaction
        $createRes = $this->postJson('/api/v1/payment/create', [
            'order_id' => $order->id,
            'gateway' => PaymentEnum::GATEWAY_VNPAY,
        ]);
        $requestId = $createRes->json('data.request_id');

        // Tạo simulated VNPay return query với HMAC-SHA512 checksum hợp lệ
        $hashSecret = config('payment.gateways.vnpay.hash_secret');
        $params = [
            'vnp_Amount' => 300000 * 100,
            'vnp_BankCode' => 'NCB',
            'vnp_CardType' => 'ATM',
            'vnp_OrderInfo' => "Thanh toan don hang {$order->code}",
            'vnp_PayDate' => now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => config('payment.gateways.vnpay.tmn_code'),
            'vnp_TransactionNo' => '14000000',
            'vnp_TxnRef' => $requestId,
        ];

        ksort($params);
        $hashData = http_build_query($params);
        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);
        $params['vnp_SecureHash'] = $secureHash;

        $returnResponse = $this->getJson('/api/v1/payment/vnpay-return?' . http_build_query($params));

        $returnResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'response_code' => '00',
            ]);

        // Kiểm tra order đã được cập nhật
        $order->refresh();
        $this->assertEquals(OrderEnum::PAYMENT_STATUS_PAID, $order->payment_status);
        $this->assertEquals(OrderEnum::STATUS_CONFIRMED, $order->status);
    }
}
