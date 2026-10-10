<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserCatalogue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacPermissionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Super Admin có full quyền truy cập các admin routes được bảo vệ
     */
    public function test_super_admin_can_access_protected_admin_routes()
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'super_admin_rbac@example.com'],
            [
                'name' => 'Super Admin RBAC',
                'password' => Hash::make('password123'),
                'user_catalogue_id' => 2,
                'publish' => 1,
            ]
        );
        $superAdmin->update(['user_catalogue_id' => 2]);

        $token = auth('admin')->claims(['guard' => 'admin'])->login($superAdmin);

        // 1. Route users
        $resUsers = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');
        $this->assertEquals(200, $resUsers->status());

        // 2. Route dashboard statistics
        $resDashboard = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/dashboard/statistics');
        $this->assertEquals(200, $resDashboard->status());
    }

    /**
     * Test nhân viên chỉ có quyền xem danh sách (paginate) thì xem được danh sách
     */
    public function test_limited_staff_user_with_paginate_permission_can_list_users()
    {
        // Tạo catalogue riêng cho staff giới hạn
        $staffCatalogue = UserCatalogue::create([
            'name' => 'Test Staff Paginate Only',
            'slug' => 'test-staff-paginate-only-' . uniqid(),
            'publish' => 1,
            'is_admin' => false,
        ]);

        // Gán chỉ quyền users:paginate
        $paginatePermission = Permission::where('name', 'users:paginate')->first();
        $this->assertNotNull($paginatePermission, 'Permission users:paginate must exist');
        $staffCatalogue->permissions()->attach($paginatePermission->id);

        $staffUser = User::create([
            'name' => 'Staff Paginate User',
            'email' => 'staff_paginate_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'user_catalogue_id' => $staffCatalogue->id,
            'publish' => 1,
        ]);

        $token = auth('admin')->claims(['guard' => 'admin'])->login($staffUser);

        // Gọi index users -> Được phép (200)
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');
        $this->assertEquals(200, $response->status());
    }

    /**
     * Test nhân viên không có quyền users:store thì bị 403 khi tạo user
     */
    public function test_limited_staff_user_without_store_permission_cannot_create_user()
    {
        $staffCatalogue = UserCatalogue::create([
            'name' => 'Test Staff No Store',
            'slug' => 'test-staff-no-store-' . uniqid(),
            'publish' => 1,
            'is_admin' => false,
        ]);

        // Chỉ gán quyền users:paginate, KHÔNG gán users:store
        $paginatePermission = Permission::where('name', 'users:paginate')->first();
        $staffCatalogue->permissions()->attach($paginatePermission->id);

        $staffUser = User::create([
            'name' => 'Staff No Store User',
            'email' => 'staff_nostore_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'user_catalogue_id' => $staffCatalogue->id,
            'publish' => 1,
        ]);

        $token = auth('admin')->claims(['guard' => 'admin'])->login($staffUser);

        // Gọi POST users -> Bị chặn 403 Forbidden
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/users', [
                'name' => 'Should Fail User',
                'email' => 'fail_' . uniqid() . '@example.com',
                'password' => 'password123',
            ]);
        $this->assertEquals(403, $response->status());
    }

    /**
     * Test nhân viên không có bất kỳ quyền nào thì bị 403 khi truy cập admin resource
     */
    public function test_staff_without_permissions_is_forbidden()
    {
        $emptyCatalogue = UserCatalogue::create([
            'name' => 'Test Empty Role',
            'slug' => 'test-empty-role-' . uniqid(),
            'publish' => 1,
            'is_admin' => false,
        ]);

        $emptyUser = User::create([
            'name' => 'Empty Role User',
            'email' => 'empty_role_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'user_catalogue_id' => $emptyCatalogue->id,
            'publish' => 1,
        ]);

        $token = auth('admin')->claims(['guard' => 'admin'])->login($emptyUser);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/users');
        $this->assertEquals(403, $response->status());
    }

    /**
     * Test khách hàng (user_catalogue_id = 1) không thể đăng nhập vào cổng admin
     */
    public function test_client_customer_cannot_login_to_admin_portal()
    {
        $password = 'password123';
        $customer = User::create([
            'name' => 'Regular Customer',
            'email' => 'customer_' . uniqid() . '@example.com',
            'password' => Hash::make($password),
            'user_catalogue_id' => 1,
            'publish' => 1,
        ]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => $customer->email,
            'password' => $password,
        ]);

        $this->assertEquals(401, $response->status());
    }
}
