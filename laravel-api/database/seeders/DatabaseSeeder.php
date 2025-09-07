<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Thanh Trung',
            'email' => 'trungptdev2004@gmail.com',
            'password' => Hash::make('thanhtrung'),
        ]);
    }
}
