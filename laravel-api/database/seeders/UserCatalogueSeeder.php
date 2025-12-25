<?php

namespace Database\Seeders;

use App\Models\UserCatalogue;
use Illuminate\Database\Seeder;

class UserCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userCatalogues = [
            [
                'name' => 'Khách hàng',
                'slug' => 'khach-hang',
                'description' => 'Nhóm người dùng là khách hàng thông thường',
                'publish' => 2,
                'is_admin' => false,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Nhóm người dùng quản trị hệ thống',
                'publish' => 2,
                'is_admin' => true,
            ],
            [
                'name' => 'Nhân viên',
                'slug' => 'nhan-vien',
                'description' => 'Nhóm người dùng là nhân viên của công ty',
                'publish' => 2,
                'is_admin' => false,
            ],
        ];

        foreach ($userCatalogues as $catalogue) {
            UserCatalogue::create($catalogue);
        }

        $this->command->info('User catalogues seeded successfully!');
    }
}
