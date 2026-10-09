<?php

namespace Database\Seeders\Attribute;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Đen",
                "description" => "Màu đen cổ điển cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "Trắng",
                "description" => "Màu trắng tinh khôi cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "Vàng",
                "description" => "Màu vàng cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "Xanh dương",
                "description" => "Màu xanh dương hiện đại cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "Đỏ",
                "description" => "Màu đỏ nổi bật cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "Xanh lá",
                "description" => "Màu xanh lá tự nhiên cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "Tím",
                "description" => "Màu tím sang trọng cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "Hồng",
                "description" => "Màu hồng dịu dàng cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "Bạc",
                "description" => "Màu bạc kim loại cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "Vàng gold",
                "description" => "Màu vàng gold cao cấp cho các sản phẩm công nghệ",
                "attribute_catalogue_id" => 1,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "4.7 inch",
                "description" => "Kích thước màn hình nhỏ gọn 4.7 inch",
                "attribute_catalogue_id" => 2,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "5.5 inch",
                "description" => "Kích thước màn hình 5.5 inch phổ biến",
                "attribute_catalogue_id" => 2,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "6.1 inch",
                "description" => "Kích thước màn hình 6.1 inch tiêu chuẩn",
                "attribute_catalogue_id" => 2,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "6.5 inch",
                "description" => "Kích thước màn hình lớn 6.5 inch",
                "attribute_catalogue_id" => 2,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "6.7 inch",
                "description" => "Kích thước màn hình 6.7 inch cho flagship",
                "attribute_catalogue_id" => 2,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "7.9 inch",
                "description" => "Kích thước màn hình 7.9 inch cho tablet nhỏ",
                "attribute_catalogue_id" => 2,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "10.2 inch",
                "description" => "Kích thước màn hình 10.2 inch cho tablet",
                "attribute_catalogue_id" => 2,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "13.3 inch",
                "description" => "Kích thước màn hình 13.3 inch cho laptop",
                "attribute_catalogue_id" => 2,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "15.6 inch",
                "description" => "Kích thước màn hình 15.6 inch phổ biến cho laptop",
                "attribute_catalogue_id" => 2,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "27 inch",
                "description" => "Kích thước màn hình 27 inch cho desktop monitor",
                "attribute_catalogue_id" => 2,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "4GB",
                "description" => "Dung lượng RAM 4GB cho thiết bị cơ bản",
                "attribute_catalogue_id" => 3,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "8GB",
                "description" => "Dung lượng RAM 8GB cho thiết bị phổ thông",
                "attribute_catalogue_id" => 3,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "16GB",
                "description" => "Dung lượng RAM 16GB cho thiết bị cao cấp",
                "attribute_catalogue_id" => 3,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "32GB",
                "description" => "Dung lượng RAM 32GB cho workstation",
                "attribute_catalogue_id" => 3,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "64GB",
                "description" => "Dung lượng ROM 64GB cho điện thoại",
                "attribute_catalogue_id" => 3,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "128GB",
                "description" => "Dung lượng ROM 128GB cho điện thoại phổ thông",
                "attribute_catalogue_id" => 3,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "256GB",
                "description" => "Dung lượng ROM 256GB cho điện thoại cao cấp",
                "attribute_catalogue_id" => 3,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "512GB",
                "description" => "Dung lượng SSD 512GB cho laptop",
                "attribute_catalogue_id" => 3,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "1TB",
                "description" => "Dung lượng SSD 1TB cho laptop gaming",
                "attribute_catalogue_id" => 3,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "2TB",
                "description" => "Dung lượng SSD 2TB cho workstation",
                "attribute_catalogue_id" => 3,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "Intel Celeron",
                "description" => "Bộ xử lý Intel Celeron cho thiết bị cơ bản",
                "attribute_catalogue_id" => 4,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "Intel Pentium",
                "description" => "Bộ xử lý Intel Pentium cho thiết bị phổ thông",
                "attribute_catalogue_id" => 4,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "Intel Core i3",
                "description" => "Bộ xử lý Intel Core i3 cho laptop văn phòng",
                "attribute_catalogue_id" => 4,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "Intel Core i5",
                "description" => "Bộ xử lý Intel Core i5 cho laptop đa năng",
                "attribute_catalogue_id" => 4,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "Intel Core i7",
                "description" => "Bộ xử lý Intel Core i7 cho laptop gaming",
                "attribute_catalogue_id" => 4,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "AMD Ryzen 3",
                "description" => "Bộ xử lý AMD Ryzen 3 cho laptop cơ bản",
                "attribute_catalogue_id" => 4,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "AMD Ryzen 5",
                "description" => "Bộ xử lý AMD Ryzen 5 cho laptop phổ thông",
                "attribute_catalogue_id" => 4,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "AMD Ryzen 7",
                "description" => "Bộ xử lý AMD Ryzen 7 cho laptop gaming",
                "attribute_catalogue_id" => 4,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "Apple M1",
                "description" => "Chip Apple M1 cho MacBook",
                "attribute_catalogue_id" => 4,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "Apple M2",
                "description" => "Chip Apple M2 cho MacBook mới",
                "attribute_catalogue_id" => 4,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "12MP",
                "description" => "Camera đơn 12MP cho điện thoại",
                "attribute_catalogue_id" => 5,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "13MP",
                "description" => "Camera 13MP cho iPhone",
                "attribute_catalogue_id" => 5,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "16MP",
                "description" => "Camera 16MP cho điện thoại tầm trung",
                "attribute_catalogue_id" => 5,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "32MP",
                "description" => "Camera 32MP cho selfie",
                "attribute_catalogue_id" => 5,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "48MP",
                "description" => "Camera 48MP cho flagship",
                "attribute_catalogue_id" => 5,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "50MP",
                "description" => "Camera 50MP cao cấp",
                "attribute_catalogue_id" => 5,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "64MP",
                "description" => "Camera 64MP siêu cao cấp",
                "attribute_catalogue_id" => 5,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "108MP",
                "description" => "Camera 108MP cho smartphone flagship",
                "attribute_catalogue_id" => 5,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "Dual 12MP",
                "description" => "Camera kép 12MP cho điện thoại",
                "attribute_catalogue_id" => 5,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "Triple 48MP",
                "description" => "Camera ba 48MP cho flagship",
                "attribute_catalogue_id" => 5,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "2000mAh",
                "description" => "Dung lượng pin 2000mAh cho thiết bị nhỏ",
                "attribute_catalogue_id" => 6,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "3000mAh",
                "description" => "Dung lượng pin 3000mAh cho điện thoại phổ thông",
                "attribute_catalogue_id" => 6,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "4000mAh",
                "description" => "Dung lượng pin 4000mAh cho điện thoại tầm trung",
                "attribute_catalogue_id" => 6,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "4500mAh",
                "description" => "Dung lượng pin 4500mAh cho điện thoại cao cấp",
                "attribute_catalogue_id" => 6,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "5000mAh",
                "description" => "Dung lượng pin 5000mAh cho flagship",
                "attribute_catalogue_id" => 6,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "6000mAh",
                "description" => "Dung lượng pin 6000mAh cho thiết bị gaming",
                "attribute_catalogue_id" => 6,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "48Wh",
                "description" => "Pin laptop 48Wh cho ultrabook",
                "attribute_catalogue_id" => 6,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "52Wh",
                "description" => "Pin laptop 52Wh tiêu chuẩn",
                "attribute_catalogue_id" => 6,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "70Wh",
                "description" => "Pin laptop 70Wh cho gaming laptop",
                "attribute_catalogue_id" => 6,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "100Wh",
                "description" => "Pin laptop 100Wh cao cấp",
                "attribute_catalogue_id" => 6,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "GTX 1650",
                "description" => "Card đồ họa NVIDIA GTX 1650 cho laptop",
                "attribute_catalogue_id" => 7,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "RTX 3050",
                "description" => "Card đồ họa NVIDIA RTX 3050 cho laptop gaming",
                "attribute_catalogue_id" => 7,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "RTX 3060",
                "description" => "Card đồ họa NVIDIA RTX 3060 cho laptop gaming",
                "attribute_catalogue_id" => 7,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "RTX 3070",
                "description" => "Card đồ họa NVIDIA RTX 3070 cho laptop cao cấp",
                "attribute_catalogue_id" => 7,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "RTX 3080",
                "description" => "Card đồ họa NVIDIA RTX 3080 cho laptop flagship",
                "attribute_catalogue_id" => 7,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "RTX 4050",
                "description" => "Card đồ họa NVIDIA RTX 4050 mới",
                "attribute_catalogue_id" => 7,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "RTX 4060",
                "description" => "Card đồ họa NVIDIA RTX 4060 cho gaming",
                "attribute_catalogue_id" => 7,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "RTX 4070",
                "description" => "Card đồ họa NVIDIA RTX 4070 cao cấp",
                "attribute_catalogue_id" => 7,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "RTX 4080",
                "description" => "Card đồ họa NVIDIA RTX 4080 flagship",
                "attribute_catalogue_id" => 7,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "RTX 4090",
                "description" => "Card đồ họa NVIDIA RTX 4090 tối tân",
                "attribute_catalogue_id" => 7,
                "order" => 10,
                "publish" => 1
            ],
            [
                "name" => "HDD 500GB",
                "description" => "Ổ cứng HDD 500GB cho desktop",
                "attribute_catalogue_id" => 8,
                "order" => 1,
                "publish" => 1
            ],
            [
                "name" => "HDD 1TB",
                "description" => "Ổ cứng HDD 1TB cho desktop",
                "attribute_catalogue_id" => 8,
                "order" => 2,
                "publish" => 1
            ],
            [
                "name" => "SSD 256GB",
                "description" => "Ổ cứng SSD 256GB cho laptop cơ bản",
                "attribute_catalogue_id" => 8,
                "order" => 3,
                "publish" => 1
            ],
            [
                "name" => "SSD 512GB",
                "description" => "Ổ cứng SSD 512GB cho laptop phổ thông",
                "attribute_catalogue_id" => 8,
                "order" => 4,
                "publish" => 1
            ],
            [
                "name" => "SSD 1TB",
                "description" => "Ổ cứng SSD 1TB cho laptop cao cấp",
                "attribute_catalogue_id" => 8,
                "order" => 5,
                "publish" => 1
            ],
            [
                "name" => "SSD 2TB",
                "description" => "Ổ cứng SSD 2TB cho workstation",
                "attribute_catalogue_id" => 8,
                "order" => 6,
                "publish" => 1
            ],
            [
                "name" => "NVMe 512GB",
                "description" => "Ổ cứng NVMe 512GB tốc độ cao",
                "attribute_catalogue_id" => 8,
                "order" => 7,
                "publish" => 1
            ],
            [
                "name" => "NVMe 1TB",
                "description" => "Ổ cứng NVMe 1TB tốc độ cao",
                "attribute_catalogue_id" => 8,
                "order" => 8,
                "publish" => 1
            ],
            [
                "name" => "NVMe 2TB",
                "description" => "Ổ cứng NVMe 2TB tốc độ cao",
                "attribute_catalogue_id" => 8,
                "order" => 9,
                "publish" => 1
            ],
            [
                "name" => "NVMe 4TB",
                "description" => "Ổ cứng NVMe 4TB cao cấp",
                "attribute_catalogue_id" => 8,
                "order" => 10,
                "publish" => 1
            ]
        ];

        foreach ($data as $item) {
            $baseSlug = Str::slug($item['name']);
            $existing = Attribute::where('slug', $baseSlug)->where('attribute_catalogue_id', '!=', $item['attribute_catalogue_id'])->exists();
            $slug = $existing ? $baseSlug . '-' . $item['attribute_catalogue_id'] : $baseSlug;

            Attribute::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'attribute_catalogue_id' => $item['attribute_catalogue_id'],
                    'order' => $item['order'],
                    'publish' => $item['publish'],
                    'user_id' => 1,
                ]
            );
        }
    }
}
