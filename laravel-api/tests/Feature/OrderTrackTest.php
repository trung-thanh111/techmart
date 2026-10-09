<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderTrackTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;
    protected User $otherClientUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientUser = User::firstOrCreate(
            ['email' => 'client_track_test@example.com'],
            ['name' => 'Client Track User', 'password' => Hash::make('secret'), 'publish' => 1]
        );

        $this->otherClientUser = User::firstOrCreate(
            ['email' => 'other_track_test@example.com'],
            ['name' => 'Other Track User', 'password' => Hash::make('secret'), 'publish' => 1]
        );
    }

    protected function createTestOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'code' => 'ORD' . rand(10000000, 99999999),
            'user_id' => $this->clientUser->id,
            'customer_name' => 'Nguyen Van A',
            'customer_email' => 'track_guest@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Trai, Ha Noi',
            'subtotal' => 500000,
            'total_amount' => 500000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'customer_note' => 'Đơn hàng test tra cứu',
        ], $attributes));
    }

    /**
     * Khách vãng lai tra cứu thành công với mã đơn và số điện thoại chính xác
     */
    public function test_guest_can_track_order_with_valid_code_and_phone(): void
    {
        $order = $this->createTestOrder([
            'customer_phone' => '0987654321',
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'code' => $order->code,
            'phone' => '0987654321',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.code', $order->code)
            ->assertJsonPath('data.customer.phone', '0987654321');
    }

    /**
     * Khách vãng lai tra cứu thành công khi số điện thoại có tiền tố +84
     */
    public function test_guest_can_track_order_with_normalized_phone_plus84(): void
    {
        $order = $this->createTestOrder([
            'customer_phone' => '0987654321',
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'code' => $order->code,
            'phone' => '+84987654321',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.code', $order->code);
    }

    /**
     * Khách vãng lai tra cứu thành công với email
     */
    public function test_guest_can_track_order_with_valid_email(): void
    {
        $order = $this->createTestOrder([
            'customer_email' => 'guest_buyer@example.com',
            'customer_phone' => '0912345678',
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'code' => $order->code,
            'email' => 'GUEST_BUYER@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.code', $order->code);
    }

    /**
     * Tra cứu qua GET method cũng hoạt động
     */
    public function test_guest_can_track_order_via_get_method(): void
    {
        $order = $this->createTestOrder([
            'customer_phone' => '0987654321',
        ]);

        $response = $this->getJson("/api/v1/orders/track?code={$order->code}&phone=0987654321");

        $response->assertStatus(200)
            ->assertJsonPath('data.code', $order->code);
    }

    /**
     * Khách vãng lai tra cứu thất bại khi sai số điện thoại (404)
     */
    public function test_guest_cannot_track_order_with_wrong_phone(): void
    {
        $order = $this->createTestOrder([
            'customer_phone' => '0987654321',
        ]);

        $response = $this->postJson('/api/v1/orders/track', [
            'code' => $order->code,
            'phone' => '0911111111',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Khách vãng lai không cung cấp phone và email thì trả về 422
     */
    public function test_guest_cannot_track_without_phone_or_email(): void
    {
        $order = $this->createTestOrder();

        $response = $this->postJson('/api/v1/orders/track', [
            'code' => $order->code,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'email']);
    }

    /**
     * Tra cứu mã đơn không tồn tại trả về 404
     */
    public function test_cannot_track_non_existent_order(): void
    {
        $response = $this->postJson('/api/v1/orders/track', [
            'code' => 'ORD-NOT-EXIST-999',
            'phone' => '0987654321',
        ]);

        $response->assertStatus(404);
    }

    /**
     * User đã đăng nhập tra cứu đơn hàng của chính mình chỉ cần mã đơn
     */
    public function test_authenticated_user_can_track_own_order_with_code_only(): void
    {
        $order = $this->createTestOrder([
            'user_id' => $this->clientUser->id,
        ]);

        $token = auth('api')->login($this->clientUser);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/orders/track', [
                'code' => $order->code,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.code', $order->code);
    }

    /**
     * User đã đăng nhập không thể xem trộm đơn của người khác nếu không khớp SĐT/email
     */
    public function test_authenticated_user_cannot_track_other_user_order_without_phone(): void
    {
        $order = $this->createTestOrder([
            'user_id' => $this->otherClientUser->id,
            'customer_phone' => '0933333333',
        ]);

        $token = auth('api')->login($this->clientUser);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/orders/track', [
                'code' => $order->code,
            ]);

        $response->assertStatus(404);
    }
}
