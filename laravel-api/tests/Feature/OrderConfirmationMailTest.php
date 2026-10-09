<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderConfirmationMailTest extends TestCase
{
    use DatabaseTransactions;

    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::firstOrCreate(
            ['slug' => 'test-mail-product'],
            [
                'name' => 'Test Mail Product',
                'sku' => 'MAIL-PROD-SKU',
                'price_origin' => 200000,
                'publish' => 2,
            ]
        );

        $this->variant = ProductVariant::firstOrCreate(
            ['sku' => 'MAIL-VARIANT-SKU'],
            [
                'product_id' => $this->product->id,
                'name' => 'Bản tiêu chuẩn',
                'price' => 200000,
                'stock' => 50,
                'publish' => 1,
            ]
        );
    }

    public function test_order_creation_queues_order_confirmation_mail(): void
    {
        Mail::fake();

        $customerEmail = 'techmart_customer@example.com';
        $payload = [
            'customer_name' => 'Nguyen Van A',
            'customer_email' => $customerEmail,
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Le Loi, Quan 1, TP HCM',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(200);
        $orderCode = $response->json('data.code');

        Mail::assertQueued(OrderConfirmationMail::class, function ($mail) use ($customerEmail, $orderCode) {
            return $mail->hasTo($customerEmail) && $mail->order->code === $orderCode;
        });
    }

    public function test_order_confirmation_mail_renders_content_correctly(): void
    {
        $order = Order::create([
            'code' => 'ORD' . rand(10000000, 99999999),
            'customer_name' => 'Tran Thi B',
            'customer_email' => 'tranthib@example.com',
            'customer_phone' => '0912345678',
            'shipping_address' => '456 Nguyen Hue, Da Nang',
            'payment_method' => 'vnpay',
            'subtotal' => 500000,
            'total_amount' => 530000,
            'shipping_fee' => 30000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $order->order_items()->create([
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => 'Tai nghe Bluetooth cao cap',
            'product_sku' => 'HEADPHONE-BT-01',
            'quantity' => 2,
            'unit_price' => 250000,
            'subtotal' => 500000,
            'total' => 500000,
        ]);

        $mailable = new OrderConfirmationMail($order);

        $rendered = $mailable->render();

        $this->assertStringContainsString($order->code, $rendered);
        $this->assertStringContainsString('Tran Thi B', $rendered);
        $this->assertStringContainsString('456 Nguyen Hue, Da Nang', $rendered);
        $this->assertStringContainsString('Tai nghe Bluetooth cao cap', $rendered);
        $this->assertStringContainsString(number_format(530000, 0, ',', '.'), $rendered);
    }

    public function test_order_creation_without_email_fails_validation_and_does_not_queue_mail(): void
    {
        Mail::fake();

        $payload = [
            'customer_name' => 'Khach Vang Lai',
            'customer_phone' => '0909090909',
            'shipping_address' => '789 Vo Van Kiet, TP HCM',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/orders', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_email']);

        Mail::assertNothingQueued();
    }
}
