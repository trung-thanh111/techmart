<?php

namespace Database\Seeders\Attribute;

use Illuminate\Database\Seeder;
use App\Models\AttributeCatalogue;
use Illuminate\Support\Str;

class AttributeCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Màu sắc",
                "description" => "Chuyên mục về kích thước màn hình cho điện thoại, tablet, laptop",
                "order" => 1,
                "publish" => 2
            ],
            [
                "name" => "Kích thước",
                "description" => "Chuyên mục về kích thước màn hình cho điện thoại, tablet, laptop",
                "order" => 1,
                "publish" => 2
            ],
            [
                "name" => "Dung lượng",
                "description" => "Chuyên mục về dung lượng RAM, ROM, SSD cho thiết bị",
                "order" => 2,
                "publish" => 2
            ],
            [
                "name" => "Cấu hình",
                "description" => "Chuyên mục về bộ xử lý, chip xử lý cho thiết bị",
                "order" => 3,
                "publish" => 2
            ],
            [
                "name" => "Camera",
                "description" => "Chuyên mục về thông số camera cho điện thoại, máy ảnh",
                "order" => 4,
                "publish" => 2
            ],
            [
                "name" => "Pin",
                "description" => "Chuyên mục về dung lượng pin cho thiết bị",
                "order" => 5,
                "publish" => 2
            ],
            [
                "name" => "Card đồ họa",
                "description" => "Chuyên mục về card đồ họa, GPU cho máy tính",
                "order" => 6,
                "publish" => 2
            ],
            [
                "name" => "Ổ cứng",
                "description" => "Chuyên mục về ổ cứng HDD, SSD cho máy tính",
                "order" => 7,
                "publish" => 2
            ]
        ];

        foreach ($data as $item) {
            AttributeCatalogue::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'order' => $item['order'],
                'publish' => $item['publish'],
                'user_id' => 1,
            ]);
        }
    }
}
