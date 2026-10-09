<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCatalogue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các user catalogue đã tạo (theo thứ tự mới: khách hàng, admin, nhân viên)
        $customerCatalogue = UserCatalogue::where('slug', 'khach-hang')->first();
        $adminCatalogue = UserCatalogue::where('slug', 'admin')->first();
        $employeeCatalogue = UserCatalogue::where('slug', 'nhan-vien')->first();

        $users = [
            [
                'name' => 'Thanh Trung',
                'first_name' => 'Thanh',
                'last_name' => 'Trung',
                'email' => 'trungptdev2004@gmail.com',
                'phone' => '0987654323',
                'password' => Hash::make('thanhtrung'),
                'user_catalogue_id' => $adminCatalogue->id,
                'gender' => 'male',
                'birthday' => '2004-01-01',
                'street' => '789 Đường DEF',
                'image' => null,
                'description' => 'Admin hệ thống',
            ],
            [
                'name' => 'Nguyễn Văn A',
                'first_name' => 'Nguyễn',
                'last_name' => 'Văn A',
                'email' => 'customer@example.com',
                'phone' => '0987654321',
                'password' => Hash::make('password123'),
                'user_catalogue_id' => $customerCatalogue->id,
                'gender' => 'male',
                'birthday' => '1990-01-01',
                'street' => '123 Đường ABC',
                'image' => null,
                'description' => 'Khách hàng mẫu',
            ],
            [
                'name' => 'Trần Thị B',
                'first_name' => 'Trần',
                'last_name' => 'Thị B',
                'email' => 'employee@example.com',
                'phone' => '0987654322',
                'password' => Hash::make('password123'),
                'user_catalogue_id' => $employeeCatalogue->id,
                'gender' => 'female',
                'birthday' => '1985-05-15',
                'street' => '456 Đường XYZ',
                'image' => null,
                'description' => 'Nhân viên mẫu',
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('Users seeded successfully!');
    }
}
