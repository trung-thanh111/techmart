<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderNoteTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;
    protected User $otherClientUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientUser = User::firstOrCreate(
            ['email' => 'client_note_test@example.com'],
            ['name' => 'Client Note User', 'password' => Hash::make('secret'), 'publish' => 1]
        );

        $this->otherClientUser = User::firstOrCreate(
            ['email' => 'other_client_note_test@example.com'],
            ['name' => 'Other Client User', 'password' => Hash::make('secret'), 'publish' => 1]
        );

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_note_test@example.com'],
            ['name' => 'Admin Note User', 'password' => Hash::make('secret'), 'publish' => 1]
        );
    }

    protected function createTestOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'code' => 'ORD' . rand(10000000, 99999999),
            'user_id' => $this->clientUser->id,
            'customer_name' => 'Client Note User',
            'customer_email' => $this->clientUser->email,
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Trai, Ha Noi',
            'subtotal' => 500000,
            'total_amount' => 500000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'customer_note' => 'Ghi chú ban đầu',
        ], $attributes));
    }

    /**
     * Khách vãng lai chưa đăng nhập không thể cập nhật ghi chú đơn hàng (401)
     */
    public function test_guest_cannot_update_order_note(): void
    {
        $response = $this->patchJson('/api/v1/orders/1/note', [
            'customer_note' => 'Ghi chú thử nghiệm',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Khách hàng có thể cập nhật ghi chú đơn hàng của chính mình khi đơn đang chờ xác nhận (pending) (200)
     */
    public function test_client_can_update_customer_note_on_own_pending_order(): void
    {
        $order = $this->createTestOrder();

        $token = auth('api')->claims(['guard' => 'api'])->login($this->clientUser);

        $newNote = 'Giao hàng giờ hành chính giúp tôi';
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/orders/{$order->id}/note", [
                'customer_note' => $newNote,
            ]);

        $response->assertStatus(200);
        $this->assertEquals($newNote, $response->json('data.customerNote'));

        $order->refresh();
        $this->assertEquals($newNote, $order->customer_note);
    }

    /**
     * Khách hàng không thể sửa ghi chú đơn hàng của khách hàng khác (403)
     */
    public function test_client_cannot_update_note_on_another_users_order(): void
    {
        $order = $this->createTestOrder([
            'user_id' => $this->otherClientUser->id,
            'customer_name' => 'Other Client',
            'customer_email' => $this->otherClientUser->email,
        ]);

        $token = auth('api')->claims(['guard' => 'api'])->login($this->clientUser);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/orders/{$order->id}/note", [
                'customer_note' => 'Hack note',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Khách hàng không thể sửa ghi chú khi đơn hàng không còn ở trạng thái pending (422)
     */
    public function test_client_cannot_update_note_on_non_pending_order(): void
    {
        $order = $this->createTestOrder([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $token = auth('api')->claims(['guard' => 'api'])->login($this->clientUser);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/orders/{$order->id}/note", [
                'customer_note' => 'Thay đổi địa chỉ giao hàng',
            ]);

        $response->assertStatus(422);
    }

    /**
     * Admin có thể cập nhật cả customer_note và admin_note trên bất kỳ đơn hàng nào (200)
     */
    public function test_admin_can_update_order_notes(): void
    {
        $order = $this->createTestOrder([
            'status' => 'shipped',
            'payment_status' => 'paid',
        ]);

        $adminToken = auth('admin')->claims(['guard' => 'admin'])->login($this->adminUser);

        $response = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->patchJson("/api/v1/orders/{$order->id}/note", [
                'customer_note' => 'Ghi chú khách được admin điều chỉnh',
                'admin_note' => 'Đã xác thực thông tin qua điện thoại',
            ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('Ghi chú khách được admin điều chỉnh', $order->customer_note);
        $this->assertEquals('Đã xác thực thông tin qua điện thoại', $order->admin_note);
    }

    /**
     * Đơn hàng không tồn tại trả về 404
     */
    public function test_update_note_returns_404_when_order_not_found(): void
    {
        $token = auth('api')->claims(['guard' => 'api'])->login($this->clientUser);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/v1/orders/99999999/note', [
                'customer_note' => 'Đơn không tồn tại',
            ]);

        $response->assertStatus(404);
    }
}
