<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use App\Models\ProductTag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                "name" => "appleiphone",
                "description" => "Tag liên quan đến điện thoại Apple iPhone",
                "publish" => 2
            ],
            [
                "name" => "samsunggalaxy",
                "description" => "Tag liên quan đến điện thoại Samsung Galaxy",
                "publish" => 2
            ],
            [
                "name" => "tabletipad",
                "description" => "Tag liên quan đến máy tính bảng Apple iPad",
                "publish" => 2
            ],
            [
                "name" => "samsungtablet",
                "description" => "Tag liên quan đến máy tính bảng Samsung Galaxy Tab",
                "publish" => 2
            ],
            [
                "name" => "laptopdell",
                "description" => "Tag liên quan đến laptop Dell",
                "publish" => 2
            ],
            [
                "name" => "laptopasus",
                "description" => "Tag liên quan đến laptop ASUS",
                "publish" => 2
            ],
            [
                "name" => "tainghejbl",
                "description" => "Tag liên quan đến thiết bị âm thanh của JBL",
                "publish" => 2
            ],
            [
                "name" => "loabose",
                "description" => "Tag liên quan đến loa tai nghe Bose",
                "publish" => 2
            ],
            [
                "name" => "micsennheiser",
                "description" => "Tag liên quan đến micro Sennheiser",
                "publish" => 2
            ],
            [
                "name" => "microde",
                "description" => "Tag micro livestream và thu âm (thương hiệu phổ biến)",
                "publish" => 2
            ],
            [
                "name" => "smartwatchgarmin",
                "description" => "Tag liên quan đến đồng hồ Garmin",
                "publish" => 2
            ],
            [
                "name" => "smartwatchfitbit",
                "description" => "Tag liên quan đến vòng đeo Fitbit",
                "publish" => 2
            ],
            [
                "name" => "cameracanon",
                "description" => "Tag liên quan đến máy ảnh Canon",
                "publish" => 2
            ],
            [
                "name" => "camerasony",
                "description" => "Tag liên quan đến camera Sony",
                "publish" => 2
            ],
            [
                "name" => "dogiadunglg",
                "description" => "Tag liên quan đến đồ gia dụng LG",
                "publish" => 2
            ],
            [
                "name" => "dogiadungsamsung",
                "description" => "Tag liên quan đến đồ gia dụng Samsung",
                "publish" => 2
            ],
            [
                "name" => "maylamdepphilips",
                "description" => "Tag cho thiết bị làm đẹp (ví dụ Philips/Amazon)",
                "publish" => 2
            ],
            [
                "name" => "maylamdepxiaomi",
                "description" => "Tag cho thiết bị làm đẹp của Xiaomi",
                "publish" => 2
            ],
            [
                "name" => "phukienapple",
                "description" => "Tag liên quan đến phụ kiện chính hãng Apple",
                "publish" => 2
            ],
            [
                "name" => "phukienlogitech",
                "description" => "Tag liên quan đến phụ kiện Logitech (chuột, bàn phím)",
                "publish" => 2
            ],
            [
                "name" => "pcgamingmsi",
                "description" => "Tag cho PC và linh kiện gaming của MSI",
                "publish" => 2
            ],
            [
                "name" => "pcdell",
                "description" => "Tag cho PC bộ và máy bàn Dell",
                "publish" => 2
            ],
            [
                "name" => "manhinhasus",
                "description" => "Tag cho màn hình thương hiệu ASUS",
                "publish" => 2
            ],
            [
                "name" => "manhinhsamsung",
                "description" => "Tag cho màn hình Samsung",
                "publish" => 2
            ],
            [
                "name" => "mayincanon",
                "description" => "Tag liên quan đến máy in Canon",
                "publish" => 2
            ],
            [
                "name" => "mayinhp",
                "description" => "Tag liên quan đến máy in HP",
                "publish" => 2
            ],
            [
                "name" => "smarttvlg",
                "description" => "Tag cho Tivi LG",
                "publish" => 2
            ],
            [
                "name" => "smarttvsamsung",
                "description" => "Tag cho Tivi Samsung",
                "publish" => 2
            ],
            [
                "name" => "thietbidienpanasonic",
                "description" => "Tag cho đồ điện gia dụng (ví dụ Panasonic/Amazon)",
                "publish" => 2
            ],
            [
                "name" => "thietbidiensharp",
                "description" => "Tag cho đồ điện gia dụng Sharp (ví dụ thương hiệu phổ biến)",
                "publish" => 2
            ]
        ];

        $userId = 1;

        foreach ($tags as $item) {
            ProductTag::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'publish' => $item['publish'],
                'user_id' => $userId,
            ]);
        }
    }
}
