<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test đăng nhập client thành công
     */
    public function test_client_login_successfully()
    {
        $password = 'password123';
        $user = User::firstOrCreate(
            ['email' => 'client_test@example.com'],
            [
                'name' => 'Client Test User',
                'password' => Hash::make($password),
                'publish' => 1,
            ]
        );

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'accessToken',
                    'tokenType',
                    'expireAt',
                    'user',
                ],
            ]);
    }

    /**
     * Test đăng nhập client thất bại khi sai mật khẩu
     */
    public function test_client_login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent_user@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test đăng nhập admin thành công
     */
    public function test_admin_login_successfully()
    {
        $password = 'thanhtrung';
        $admin = User::firstOrCreate(
            ['email' => 'admin_test@example.com'],
            [
                'name' => 'Admin Test User',
                'password' => Hash::make($password),
                'publish' => 1,
            ]
        );

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => $admin->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'accessToken',
                    'tokenType',
                    'expireAt',
                    'user',
                ],
            ]);
    }

    /**
     * Test các endpoint public không bị chặn bởi jwt:admin (không bị 401)
     */
    public function test_public_endpoints_accessible_without_token()
    {
        $endpoints = [
            '/api/v1/menus',
            '/api/v1/products',
            '/api/v1/brands',
            '/api/v1/banners',
            '/api/v1/systems',
            '/api/v1/faqs',
            '/api/v1/posts',
            '/api/v1/product_catalogues',
            '/api/v1/post_catalogues',
            '/api/v1/attribute_catalogues',
            '/api/v1/attributes',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $this->assertNotEquals(
                401,
                $response->status(),
                "Endpoint {$endpoint} should be public and not return 401 Unauthorized"
            );
        }
    }

    /**
     * Test các endpoint admin được bảo vệ chặt chẽ bằng middleware jwt:admin
     */
    public function test_admin_mutation_endpoints_require_admin_jwt()
    {
        // 1. Không gửi token -> 401 access_token_not_found
        $responseNoToken = $this->postJson('/api/v1/menus', ['name' => 'New Menu']);
        $responseNoToken->assertStatus(401);

        // 2. Gửi token client (guard api) vào endpoint admin -> 401 token_invalid
        $clientUser = User::firstOrCreate(
            ['email' => 'guard_test@example.com'],
            ['name' => 'Guard Test User', 'password' => Hash::make('secret'), 'publish' => 1]
        );
        $clientToken = auth('api')->claims(['guard' => 'api'])->login($clientUser);

        $responseClientToken = $this->withHeader('Authorization', "Bearer {$clientToken}")
            ->postJson('/api/v1/menus', ['name' => 'New Menu']);
        $responseClientToken->assertStatus(401);
    }

    /**
     * Test orders index hoạt động với cả token client và admin
     */
    public function test_orders_index_with_client_and_admin_token()
    {
        // Không có token -> 401
        $this->getJson('/api/v1/orders')->assertStatus(401);

        $user = User::firstOrCreate(
            ['email' => 'order_user@example.com'],
            ['name' => 'Order User', 'password' => Hash::make('secret'), 'publish' => 1]
        );

        // Token client (api guard)
        $clientToken = auth('api')->claims(['guard' => 'api'])->login($user);
        $resClient = $this->withHeader('Authorization', "Bearer {$clientToken}")
            ->getJson('/api/v1/orders');
        $this->assertEquals(200, $resClient->status());

        // Token admin (admin guard)
        $adminToken = auth('admin')->claims(['guard' => 'admin'])->login($user);
        $resAdmin = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->getJson('/api/v1/orders');
        $this->assertEquals(200, $resAdmin->status());
    }
}
