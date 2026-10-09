<?php

namespace Database\Seeders\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Seed main-menu and hierarchy items.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MenuItem::truncate();
        Menu::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menu = Menu::create([
            'name' => 'Menu Chính',
            'slug' => 'main-menu',
            'position' => 'header',
            'publish' => 2,
        ]);

        $menuStructure = [
            [
                'name' => 'Điện thoại',
                'slug' => 'category/dien-thoai',
                'children' => [
                    ['name' => 'iPhone (Apple)', 'slug' => 'category/dien-thoai?brand=apple'],
                    ['name' => 'Samsung Galaxy', 'slug' => 'category/dien-thoai?brand=samsung'],
                    ['name' => 'Xiaomi / Redmi', 'slug' => 'category/dien-thoai?brand=xiaomi'],
                ],
            ],
            [
                'name' => 'Laptop',
                'slug' => 'category/laptop',
                'children' => [
                    ['name' => 'Apple MacBook', 'slug' => 'category/laptop?brand=apple'],
                    ['name' => 'Asus ROG & Gaming', 'slug' => 'category/laptop?brand=asus'],
                    ['name' => 'Dell & Doanh nhân', 'slug' => 'category/laptop?brand=dell'],
                    ['name' => 'Lenovo ThinkPad', 'slug' => 'category/laptop?brand=lenovo'],
                ],
            ],
            [
                'name' => 'Tablet',
                'slug' => 'category/tablet',
                'children' => [
                    ['name' => 'iPad Pro / Air / Gen 10', 'slug' => 'category/tablet?brand=apple'],
                    ['name' => 'Samsung Galaxy Tab', 'slug' => 'category/tablet?brand=samsung'],
                    ['name' => 'Xiaomi Pad', 'slug' => 'category/tablet?brand=xiaomi'],
                ],
            ],
            [
                'name' => 'Thiết bị âm thanh',
                'slug' => 'category/am-thanh',
                'children' => [
                    ['name' => 'Tai nghe AirPods', 'slug' => 'category/am-thanh?brand=apple'],
                    ['name' => 'Tai nghe Sony', 'slug' => 'category/am-thanh?brand=sony'],
                    ['name' => 'Loa Marshall', 'slug' => 'category/am-thanh?brand=marshall'],
                    ['name' => 'Loa JBL', 'slug' => 'category/am-thanh?brand=jbl'],
                ],
            ],
            [
                'name' => 'Đồng hồ thông minh',
                'slug' => 'category/dong-ho-thong-minh',
                'children' => [
                    ['name' => 'Apple Watch Ultra / Series', 'slug' => 'category/dong-ho-thong-minh?brand=apple'],
                    ['name' => 'Samsung Galaxy Watch', 'slug' => 'category/dong-ho-thong-minh?brand=samsung'],
                ],
            ],
            [
                'name' => 'Phụ kiện công nghệ',
                'slug' => 'category/phu-kien',
                'children' => [
                    ['name' => 'Củ sạc & Cáp sạc nhanh', 'slug' => 'category/phu-kien?brand=anker'],
                    ['name' => 'Pin sạc dự phòng', 'slug' => 'category/phu-kien?brand=baseus'],
                    ['name' => 'Chuột & Bàn phím cơ', 'slug' => 'category/phu-kien?brand=logitech'],
                    ['name' => 'Màn hình máy tính', 'slug' => 'category/phu-kien?brand=asus'],
                ],
            ],
        ];

        $order = 1;
        $cursor = 1;

        foreach ($menuStructure as $l1) {
            $lft1 = $cursor++;
            $parentItem = MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => 0,
                'name' => $l1['name'],
                'slug' => $l1['slug'],
                'level' => 1,
                'order' => $order++,
                'publish' => 2,
                'lft' => $lft1,
                'rgt' => $lft1 + 1, // updated after children
            ]);

            $subOrder = 1;
            foreach ($l1['children'] as $l2) {
                $lft2 = $cursor++;
                $rgt2 = $cursor++;
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'parent_id' => $parentItem->id,
                    'name' => $l2['name'],
                    'slug' => $l2['slug'],
                    'level' => 2,
                    'order' => $subOrder++,
                    'publish' => 2,
                    'lft' => $lft2,
                    'rgt' => $rgt2,
                ]);
            }

            $rgt1 = $cursor++;
            $parentItem->update(['rgt' => $rgt1]);
        }
    }
}
