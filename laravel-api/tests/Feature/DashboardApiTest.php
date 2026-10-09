<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_dashboard_test@example.com'],
            [
                'name' => 'Admin Dashboard Tester',
                'password' => Hash::make('password123'),
                'publish' => 1,
                'user_catalogue_id' => 2,
            ]
        );

        $this->adminToken = (string) auth('admin')->claims(['guard' => 'admin'])->login($this->adminUser);
    }

    public function test_guest_cannot_access_dashboard_statistics(): void
    {
        $response = $this->getJson('/api/v1/dashboard/statistics');
        $response->assertStatus(401);
    }

    public function test_client_token_cannot_access_dashboard_statistics(): void
    {
        $clientUser = User::firstOrCreate(
            ['email' => 'client_dash_test@example.com'],
            [
                'name' => 'Client Dash Tester',
                'password' => Hash::make('password123'),
                'publish' => 1,
                'user_catalogue_id' => 1,
            ]
        );

        $clientToken = (string) auth('api')->claims(['guard' => 'api'])->login($clientUser);

        $response = $this->withHeader('Authorization', "Bearer {$clientToken}")
            ->getJson('/api/v1/dashboard/statistics');

        $response->assertStatus(401);
    }

    public function test_admin_can_access_dashboard_statistics(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/dashboard/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'data' => [
                    'overview' => [
                        'earnings' => [
                            'total',
                            'this_month',
                            'last_month',
                            'growth_percentage',
                        ],
                        'orders' => [
                            'total',
                            'this_month',
                            'last_month',
                            'growth_percentage',
                            'pending',
                            'completed',
                            'cancelled',
                        ],
                        'customers' => [
                            'total',
                            'this_month',
                            'last_month',
                            'growth_percentage',
                        ],
                        'products' => [
                            'total',
                            'low_stock_count',
                        ],
                    ],
                    'status_distribution' => [
                        '*' => [
                            'status',
                            'label',
                            'count',
                        ],
                    ],
                ],
            ]);
    }

    public function test_admin_can_access_dashboard_charts(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/dashboard/charts?range=monthly');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'data' => [
                    'categories',
                    'series' => [
                        '*' => [
                            'name',
                            'data',
                        ],
                    ],
                ],
            ]);

        $categories = $response->json('data.categories');
        $this->assertCount(12, $categories);
    }

    public function test_admin_can_access_recent_orders(): void
    {
        $order = Order::create([
            'code' => 'ORD' . rand(10000000, 99999999),
            'customer_name' => 'Recent Order Customer',
            'customer_email' => 'recent_cust@example.com',
            'customer_phone' => '0988776655',
            'shipping_address' => '123 Đường Test, Hà Nội',
            'subtotal' => 1000000,
            'total_amount' => 1050000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/dashboard/recent-orders?limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'customer_name',
                        'total_amount',
                        'status',
                        'payment_status',
                    ],
                ],
            ]);

        $codes = collect($response->json('data'))->pluck('code')->toArray();
        $this->assertContains($order->code, $codes);
    }

    public function test_admin_can_access_top_products(): void
    {
        $product = Product::create([
            'sku' => 'SKU' . rand(10000, 99999),
            'name' => 'Top Seller Product ' . rand(1000, 9999),
            'slug' => 'top-seller-' . uniqid(),
            'price' => 500000,
            'price_origin' => 500000,
            'sold' => 99,
            'publish' => 2,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/dashboard/top-products?limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'total_sold',
                    ],
                ],
            ]);
    }
}
