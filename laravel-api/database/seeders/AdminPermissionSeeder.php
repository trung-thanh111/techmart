<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserCatalogue;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tìm user catalogue admin
        $adminCatalogue = UserCatalogue::where('slug', 'admin')->first();

        if (!$adminCatalogue) {
            $this->command->error('Admin user catalogue not found!');
            return;
        }

        // Lấy tất cả permissions
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->command->warn('No permissions found. Please run php artisan generate:permissions-for-modules first.');
            return;
        }

        // Attach tất cả permissions cho admin catalogue
        $adminCatalogue->permissions()->sync($permissions->pluck('id')->toArray());

        $this->command->info("Assigned {$permissions->count()} permissions to admin catalogue: {$adminCatalogue->name}");
    }
}
