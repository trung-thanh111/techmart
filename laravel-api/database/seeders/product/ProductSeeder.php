<?php

namespace Database\Seeders\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCatalogue;
use App\Models\Attribute;
use App\Models\AttributeCatalogue;
use App\Models\ProductVariant;
use App\Models\ProductTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dọn sạch dữ liệu sản phẩm cũ (Clean Database)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_variant_attribute')->truncate();
        DB::table('product_variants')->truncate();
        DB::table('product_tag_product')->truncate();
        DB::table('product_catalogue_product')->truncate();
        DB::table('product_faqs')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Chuẩn bị Attribute Catalogues
        $attrCatGeneral = AttributeCatalogue::firstOrCreate(
            ['slug' => 'thong-so-chung'],
            ['name' => 'Thông số chung', 'publish' => 1, 'user_id' => 1]
        );
        $attrCatColor = AttributeCatalogue::firstOrCreate(
            ['slug' => 'mau-sac'],
            ['name' => 'Màu sắc', 'publish' => 1, 'user_id' => 1]
        );
        $attrCatStorage = AttributeCatalogue::firstOrCreate(
            ['slug' => 'dung-luong'],
            ['name' => 'Dung lượng', 'publish' => 1, 'user_id' => 1]
        );
        $attrCatRam = AttributeCatalogue::firstOrCreate(
            ['slug' => 'cau-hinh'],
            ['name' => 'Cấu hình', 'publish' => 1, 'user_id' => 1]
        );

        // 3. Chuẩn bị Brands
        $brandNames = [
            'Apple' => ['country' => 'USA', 'website' => 'https://apple.com'],
            'Samsung' => ['country' => 'Hàn Quốc', 'website' => 'https://samsung.com'],
            'Xiaomi' => ['country' => 'Trung Quốc', 'website' => 'https://mi.com'],
            'Oppo' => ['country' => 'Trung Quốc', 'website' => 'https://oppo.com'],
            'Asus' => ['country' => 'Đài Loan', 'website' => 'https://asus.com'],
            'Dell' => ['country' => 'USA', 'website' => 'https://dell.com'],
            'Lenovo' => ['country' => 'Trung Quốc', 'website' => 'https://lenovo.com'],
            'HP' => ['country' => 'USA', 'website' => 'https://hp.com'],
            'Sony' => ['country' => 'Nhật Bản', 'website' => 'https://sony.com'],
            'JBL' => ['country' => 'USA', 'website' => 'https://jbl.com'],
            'Marshall' => ['country' => 'Anh Quốc', 'website' => 'https://marshallheadphones.com'],
            'Logitech' => ['country' => 'Thụy Sĩ', 'website' => 'https://logitech.com'],
            'Anker' => ['country' => 'USA', 'website' => 'https://anker.com'],
            'Baseus' => ['country' => 'Trung Quốc', 'website' => 'https://baseus.com'],
            'Ugreen' => ['country' => 'Trung Quốc', 'website' => 'https://ugreen.com'],
        ];

        foreach ($brandNames as $name => $meta) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'country' => $meta['country'],
                    'website' => $meta['website'],
                    'publish' => 2,
                    'user_id' => 1,
                ]
            );
        }

        // 4. Chuẩn bị Product Catalogues
        $catalogues = [
            ['slug' => 'dien-thoai', 'name' => 'Điện thoại', 'order' => 1],
            ['slug' => 'laptop', 'name' => 'Laptop', 'order' => 2],
            ['slug' => 'tablet', 'name' => 'Tablet', 'order' => 3],
            ['slug' => 'am-thanh', 'name' => 'Âm thanh', 'order' => 4],
            ['slug' => 'dong-ho-thong-minh', 'name' => 'Đồng hồ thông minh', 'order' => 5],
            ['slug' => 'phu-kien', 'name' => 'Phụ kiện công nghệ', 'order' => 6],
        ];
        foreach ($catalogues as $cat) {
            ProductCatalogue::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'order' => $cat['order'], 'publish' => 2, 'level' => 1, 'user_id' => 1]
            );
        }

        // 5. Chuẩn bị Product Tags
        $tagsMap = [
            'appleiphone' => 'Apple iPhone',
            'samsunggalaxy' => 'Samsung Galaxy',
            'laptopdell' => 'Laptop Dell',
            'laptopasus' => 'Laptop Asus',
            'tainghe' => 'Tai nghe',
            'loa' => 'Loa Bluetooth',
            'flagship' => 'Flagship Cao Cấp',
            'giare' => 'Giá tốt sinh viên',
            'gaming' => 'Gaming Gear',
            'phukien' => 'Phụ kiện chính hãng',
        ];
        foreach ($tagsMap as $slug => $name) {
            ProductTag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'publish' => 2, 'is_hot' => 1, 'user_id' => 1]
            );
        }

        // 6. Danh sách 30 sản phẩm thực tế CellphoneS / FPT Shop chuẩn từng field
        $products = [
            // ================= SMARTPHONES =================
            [
                'name' => 'iPhone 16 Pro Max',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 34990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 480,
                'like_count' => 310,
                'warranty' => 12,
                'tag_slugs' => ['appleiphone', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-pro-max.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-pro-max.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/v/n/vn_iphone_16_pro_max_desert_titanium_pdp_image_position_1__vn-vi_1.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/v/n/vn_iphone_16_pro_max_desert_titanium_pdp_image_position_1__vn-vi_1.jpg']
                ],
                'description' => 'iPhone 16 Pro Max trang bị chip Apple A18 Pro mạnh mẽ, camera điều khiển cảm ứng mới, vỏ Titanium chuẩn hàng không vũ trụ và màn hình Super Retina XDR 6.9 inch viền siêu mỏng.',
                'content' => '<p><strong>iPhone 16 Pro Max</strong> là đỉnh cao công nghệ của Apple với màn hình 6.9 inch viền mỏng nhất từ trước đến nay. Nút Action Button đa năng cùng Camera Control thế hệ mới đem đến trải nghiệm nhiếp ảnh chuyên nghiệp chuẩn Studio.</p><p>Thời lượng pin kỷ lục cùng sạc nhanh MagSafe thế hệ mới mang lại độ tin cậy vượt bậc.</p>',
                'variants' => [
                    ['sku' => 'IP16PM-256-DES', 'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'price' => 34490000, 'stock' => 50, 'attrs' => ['256GB', 'Titan Sa Mạc']],
                    ['sku' => 'IP16PM-256-NAT', 'name' => 'iPhone 16 Pro Max 256GB Titan Tự Nhiên', 'price' => 34490000, 'stock' => 45, 'attrs' => ['256GB', 'Titan Tự Nhiên']],
                    ['sku' => 'IP16PM-256-BLK', 'name' => 'iPhone 16 Pro Max 256GB Titan Đen', 'price' => 34490000, 'stock' => 40, 'attrs' => ['256GB', 'Titan Đen']],
                    ['sku' => 'IP16PM-512-DES', 'name' => 'iPhone 16 Pro Max 512GB Titan Sa Mạc', 'price' => 39990000, 'stock' => 30, 'attrs' => ['512GB', 'Titan Sa Mạc']],
                    ['sku' => 'IP16PM-512-NAT', 'name' => 'iPhone 16 Pro Max 512GB Titan Tự Nhiên', 'price' => 39990000, 'stock' => 35, 'attrs' => ['512GB', 'Titan Tự Nhiên']],
                    ['sku' => 'IP16PM-512-BLK', 'name' => 'iPhone 16 Pro Max 512GB Titan Đen', 'price' => 39990000, 'stock' => 25, 'attrs' => ['512GB', 'Titan Đen']],
                    ['sku' => 'IP16PM-1TB-DES', 'name' => 'iPhone 16 Pro Max 1TB Titan Sa Mạc', 'price' => 45990000, 'stock' => 15, 'attrs' => ['1TB', 'Titan Sa Mạc']],
                    ['sku' => 'IP16PM-1TB-NAT', 'name' => 'iPhone 16 Pro Max 1TB Titan Tự Nhiên', 'price' => 45990000, 'stock' => 15, 'attrs' => ['1TB', 'Titan Tự Nhiên']],
                    ['sku' => 'IP16PM-1TB-BLK', 'name' => 'iPhone 16 Pro Max 1TB Titan Đen', 'price' => 45990000, 'stock' => 20, 'attrs' => ['1TB', 'Titan Đen']],
                ]
            ],
            [
                'name' => 'iPhone 16',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 22990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 290,
                'like_count' => 185,
                'warranty' => 12,
                'tag_slugs' => ['appleiphone'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-1.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-1.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-2.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-16-2.png']
                ],
                'description' => 'iPhone 16 hoàn toàn mới trang bị chip A18 tiến trình 3nm, cụm camera kép bố trí dọc hỗ trợ quay Spatial Video cho Apple Vision Pro, màu sắc trẻ trung bắt mắt.',
                'content' => '<p><strong>iPhone 16</strong> sở hữu thiết kế thời thượng với bảng màu pastel rực rỡ, kính pha màu bền bỉ gấp 2 lần. Cảm biến chính Fusion 48MP cho ảnh chụp độ phân giải siêu nét.</p>',
                'variants' => [
                    ['sku' => 'IP16-128-PNK', 'name' => 'iPhone 16 128GB Hồng', 'price' => 21990000, 'stock' => 60, 'attrs' => ['128GB', 'Hồng']],
                    ['sku' => 'IP16-128-BLU', 'name' => 'iPhone 16 128GB Xanh Dương', 'price' => 21990000, 'stock' => 50, 'attrs' => ['128GB', 'Xanh Dương']],
                    ['sku' => 'IP16-256-PNK', 'name' => 'iPhone 16 256GB Hồng', 'price' => 24990000, 'stock' => 40, 'attrs' => ['256GB', 'Hồng']],
                    ['sku' => 'IP16-256-TEA', 'name' => 'iPhone 16 256GB Xanh Dương', 'price' => 24990000, 'stock' => 40, 'attrs' => ['256GB', 'Xanh Dương']],
                ]
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'brand_name' => 'Samsung',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 33990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 395,
                'like_count' => 275,
                'warranty' => 12,
                'tag_slugs' => ['samsunggalaxy', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/s/ss-s24-ultra-xam-222.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/s/ss-s24-ultra-xam-222.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/s/ss-s24-ultra-den-222.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/s/ss-s24-ultra-den-222.png']
                ],
                'description' => 'Galaxy S24 Ultra mở ra kỷ nguyên Galaxy AI đột phá với tính năng Khoanh tròn tìm kiếm, Trợ lý ghi chú, Dịch trực tiếp. Khung viền Titan sang trọng và bút S-Pen tích hợp.',
                'content' => '<p><strong>Samsung Galaxy S24 Ultra</strong> tích hợp vi xử lý Snapdragon 8 Gen 3 for Galaxy tối ưu hóa đồ họa ray tracing đỉnh cao. Camera 200MP bắt trọn từng khoảnh khắc đêm tuyệt hảo.</p>',
                'variants' => [
                    ['sku' => 'S24U-256-GRY', 'name' => 'Galaxy S24 Ultra 256GB Xám Titan', 'price' => 28990000, 'stock' => 55, 'attrs' => ['256GB', 'Xám Titan']],
                    ['sku' => 'S24U-256-BLK', 'name' => 'Galaxy S24 Ultra 256GB Đen Titan', 'price' => 28990000, 'stock' => 45, 'attrs' => ['256GB', 'Đen Titan']],
                    ['sku' => 'S24U-512-GRY', 'name' => 'Galaxy S24 Ultra 512GB Xám Titan', 'price' => 32490000, 'stock' => 30, 'attrs' => ['512GB', 'Xám Titan']],
                    ['sku' => 'S24U-512-BLK', 'name' => 'Galaxy S24 Ultra 512GB Đen Titan', 'price' => 32490000, 'stock' => 30, 'attrs' => ['512GB', 'Đen Titan']],
                ]
            ],
            [
                'name' => 'Samsung Galaxy Z Fold5',
                'brand_name' => 'Samsung',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 40990000,
                'is_hot' => false,
                'season' => 'winter',
                'sold' => 140,
                'like_count' => 95,
                'warranty' => 12,
                'tag_slugs' => ['samsunggalaxy', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-z-fold-5-256gb_1.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-z-fold-5-256gb_1.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-z-fold-5-512gb.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-z-fold-5-512gb.png']
                ],
                'description' => 'Tuyệt tác màn hình gập không kẽ hở Flex Hinge tiên tiến. Màn hình cực đại 7.6 inch Dynamic AMOLED 2X 120Hz đa nhiệm đỉnh cao.',
                'content' => '<p><strong>Galaxy Z Fold5</strong> nâng tầm hiệu suất làm việc với thanh tác vụ Taskbar đa nhiệm thông minh, hỗ trợ chạy 3 ứng dụng cùng lúc mượt mà.</p>',
                'variants' => [
                    ['sku' => 'ZF5-256-BLU', 'name' => 'Galaxy Z Fold5 256GB Xanh Icy', 'price' => 31990000, 'stock' => 25, 'attrs' => ['256GB', 'Xanh Dương']],
                    ['sku' => 'ZF5-256-BLK', 'name' => 'Galaxy Z Fold5 256GB Đen Phantom', 'price' => 31990000, 'stock' => 20, 'attrs' => ['256GB', 'Đen']],
                    ['sku' => 'ZF5-512-BLU', 'name' => 'Galaxy Z Fold5 512GB Xanh Icy', 'price' => 35990000, 'stock' => 15, 'attrs' => ['512GB', 'Xanh Dương']],
                    ['sku' => 'ZF5-512-BLK', 'name' => 'Galaxy Z Fold5 512GB Đen Phantom', 'price' => 35990000, 'stock' => 0, 'attrs' => ['512GB', 'Đen']],
                ]
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'brand_name' => 'Xiaomi',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 32990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 175,
                'like_count' => 160,
                'warranty' => 18,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-14-ultra_1__2.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-14-ultra_1__2.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-14-ultra-trang.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-14-ultra-trang.png']
                ],
                'description' => 'Siêu phẩm nhiếp ảnh di động hợp tác cùng Leica với ống kính quang học Summilux thế hệ mới, cảm biến 1 inch LYT-900 và khẩu độ biến thiên vô cấp f/1.63 - f/4.0.',
                'content' => '<p><strong>Xiaomi 14 Ultra</strong> là chiếc smartphone quay chụp chuyên nghiệp với hệ thống 4 camera 50MP bao phủ mọi dải tiêu cự từ 12mm đến 120mm.</p>',
                'variants' => [
                    ['sku' => 'MI14U-512-BLK', 'name' => 'Xiaomi 14 Ultra 512GB Đen', 'price' => 29990000, 'stock' => 30, 'attrs' => ['512GB', 'Đen']],
                    ['sku' => 'MI14U-512-WHT', 'name' => 'Xiaomi 14 Ultra 512GB Trắng', 'price' => 29990000, 'stock' => 20, 'attrs' => ['512GB', 'Trắng']],
                ]
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro 5G',
                'brand_name' => 'Xiaomi',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 9490000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 520,
                'like_count' => 210,
                'warranty' => 18,
                'tag_slugs' => ['giare'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-redmi-note-13-pro-plus_1_.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-redmi-note-13-pro-plus_1_.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-redmi-note-13-pro-5g.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-redmi-note-13-pro-5g.png']
                ],
                'description' => 'Điện thoại quốc dân phân khúc tầm trung với camera 200MP chống rung OIS, màn hình 1.5K AMOLED 120Hz sắc nét và sạc nhanh turbo 67W.',
                'content' => '<p><strong>Redmi Note 13 Pro 5G</strong> mang lại trải nghiệm màn hình viền mỏng ấn tượng, chip Snapdragon 7s Gen 2 mạnh mẽ chơi game mượt mà.</p>',
                'variants' => [
                    ['sku' => 'RN13P-128-BLK', 'name' => 'Redmi Note 13 Pro 5G 128GB Đen', 'price' => 8490000, 'stock' => 80, 'attrs' => ['128GB', 'Đen']],
                    ['sku' => 'RN13P-128-BLU', 'name' => 'Redmi Note 13 Pro 5G 128GB Xanh Đại Dương', 'price' => 8490000, 'stock' => 75, 'attrs' => ['128GB', 'Xanh Dương']],
                    ['sku' => 'RN13P-256-BLK', 'name' => 'Redmi Note 13 Pro 5G 256GB Đen', 'price' => 8990000, 'stock' => 70, 'attrs' => ['256GB', 'Đen']],
                    ['sku' => 'RN13P-256-BLU', 'name' => 'Redmi Note 13 Pro 5G 256GB Xanh Đại Dương', 'price' => 8990000, 'stock' => 70, 'attrs' => ['256GB', 'Xanh Dương']],
                ]
            ],
            [
                'name' => 'iPhone 13',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 15990000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 680,
                'like_count' => 340,
                'warranty' => 12,
                'tag_slugs' => ['appleiphone', 'giare'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-13_2_.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-13_2_.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/1/4/14_1_9_2_7.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/1/4/14_1_9_2_7.jpg']
                ],
                'description' => 'iPhone 13 vẫn là lựa chọn bán chạy số 1 nhờ thiết kế nhỏ gọn sang trọng, thời lượng pin trâu và hiệu năng A15 Bionic bền bỉ nhiều năm.',
                'content' => '<p><strong>iPhone 13</strong> phù hợp cho người dùng tìm kiếm thiết bị ổn định, camera sắc nét và hỗ trợ cập nhật iOS lâu dài với mức giá cực kỳ dễ tiếp cận.</p>',
                'variants' => [
                    ['sku' => 'IP13-128-WHT', 'name' => 'iPhone 13 128GB Trắng Starlight', 'price' => 13490000, 'stock' => 90, 'attrs' => ['128GB', 'Trắng']],
                    ['sku' => 'IP13-128-BLK', 'name' => 'iPhone 13 128GB Đen Midnight', 'price' => 13490000, 'stock' => 85, 'attrs' => ['128GB', 'Đen']],
                ]
            ],

            // ================= LAPTOPS =================
            [
                'name' => 'MacBook Pro 14 inch M3 Pro',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'laptop',
                'price_origin' => 49990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 120,
                'like_count' => 190,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook_13_19.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook_13_19.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook-pro-14-m3-pro-silver.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook-pro-14-m3-pro-silver.png']
                ],
                'description' => 'MacBook Pro 14 inch với chip M3 Pro mang lại hiệu năng đồ họa đột phá, màn hình Liquid Retina XDR độ sáng 1600 nits và màu sắc Đen Không Gian tuyệt đẹp.',
                'content' => '<p><strong>MacBook Pro M3 Pro</strong> được chế tác cho lập trình viên, nhà dựng phim 4K/8K và đồ họa 3D. Kiến trúc GPU thế hệ mới hỗ trợ dò tia tăng tốc phần cứng.</p>',
                'variants' => [
                    ['sku' => 'MBP-M3P-18-512-BLK', 'name' => 'MacBook Pro 14 M3 Pro 18GB 512GB Đen', 'price' => 48490000, 'stock' => 20, 'attrs' => ['18GB', '512GB', 'Đen']],
                    ['sku' => 'MBP-M3P-18-512-SIL', 'name' => 'MacBook Pro 14 M3 Pro 18GB 512GB Bạc', 'price' => 48490000, 'stock' => 0, 'attrs' => ['18GB', '512GB', 'Bạc']],
                    ['sku' => 'MBP-M3P-18-1TB-BLK', 'name' => 'MacBook Pro 14 M3 Pro 18GB 1TB Đen', 'price' => 53990000, 'stock' => 15, 'attrs' => ['18GB', '1TB', 'Đen']],
                    ['sku' => 'MBP-M3P-18-1TB-SIL', 'name' => 'MacBook Pro 14 M3 Pro 18GB 1TB Bạc', 'price' => 53990000, 'stock' => 15, 'attrs' => ['18GB', '1TB', 'Bạc']],
                    ['sku' => 'MBP-M3P-36-512-BLK', 'name' => 'MacBook Pro 14 M3 Pro 36GB 512GB Đen', 'price' => 54490000, 'stock' => 12, 'attrs' => ['36GB', '512GB', 'Đen']],
                    ['sku' => 'MBP-M3P-36-512-SIL', 'name' => 'MacBook Pro 14 M3 Pro 36GB 512GB Bạc', 'price' => 54490000, 'stock' => 10, 'attrs' => ['36GB', '512GB', 'Bạc']],
                    ['sku' => 'MBP-M3P-36-1TB-BLK', 'name' => 'MacBook Pro 14 M3 Pro 36GB 1TB Đen', 'price' => 59990000, 'stock' => 10, 'attrs' => ['36GB', '1TB', 'Đen']],
                    ['sku' => 'MBP-M3P-36-1TB-SIL', 'name' => 'MacBook Pro 14 M3 Pro 36GB 1TB Bạc', 'price' => 59990000, 'stock' => 12, 'attrs' => ['36GB', '1TB', 'Bạc']],
                ]
            ],
            [
                'name' => 'MacBook Air 13 inch M3',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'laptop',
                'price_origin' => 27990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 310,
                'like_count' => 280,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook_13_18_1.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook_13_18_1.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook-air-m3-13-spacegray.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook-air-m3-13-spacegray.png']
                ],
                'description' => 'MacBook Air M3 siêu mỏng nhẹ chỉ 1.24kg, pin lên đến 18 giờ liên tục, hỗ trợ xuất 2 màn hình ngoài khi gập máy.',
                'content' => '<p><strong>MacBook Air M3</strong> là chiếc máy tính xách tay hoàn hảo cho sinh viên và giới văn phòng với vỏ nhôm nguyên khối tái chế 100%, bàn phím Magic Keyboard êm ái.</p>',
                'variants' => [
                    ['sku' => 'MBA-M3-8-256-GRY', 'name' => 'MacBook Air 13 M3 8GB 256GB Xám', 'price' => 26490000, 'stock' => 45, 'attrs' => ['8GB', '256GB', 'Xám Titan']],
                    ['sku' => 'MBA-M3-8-256-BLK', 'name' => 'MacBook Air 13 M3 8GB 256GB Midnight', 'price' => 26490000, 'stock' => 40, 'attrs' => ['8GB', '256GB', 'Đen']],
                    ['sku' => 'MBA-M3-8-512-GRY', 'name' => 'MacBook Air 13 M3 8GB 512GB Xám', 'price' => 29490000, 'stock' => 25, 'attrs' => ['8GB', '512GB', 'Xám Titan']],
                    ['sku' => 'MBA-M3-8-512-BLK', 'name' => 'MacBook Air 13 M3 8GB 512GB Midnight', 'price' => 29490000, 'stock' => 25, 'attrs' => ['8GB', '512GB', 'Đen']],
                    ['sku' => 'MBA-M3-16-256-GRY', 'name' => 'MacBook Air 13 M3 16GB 256GB Xám', 'price' => 28990000, 'stock' => 30, 'attrs' => ['16GB', '256GB', 'Xám Titan']],
                    ['sku' => 'MBA-M3-16-256-BLK', 'name' => 'MacBook Air 13 M3 16GB 256GB Midnight', 'price' => 28990000, 'stock' => 30, 'attrs' => ['16GB', '256GB', 'Đen']],
                    ['sku' => 'MBA-M3-16-512-GRY', 'name' => 'MacBook Air 13 M3 16GB 512GB Xám', 'price' => 31990000, 'stock' => 20, 'attrs' => ['16GB', '512GB', 'Xám Titan']],
                    ['sku' => 'MBA-M3-16-512-MID', 'name' => 'MacBook Air 13 M3 16GB 512GB Midnight', 'price' => 31990000, 'stock' => 30, 'attrs' => ['16GB', '512GB', 'Đen']],
                ]
            ],
            [
                'name' => 'Asus ROG Zephyrus G14 OLED GA403',
                'brand_name' => 'Asus',
                'catalogue_slug' => 'laptop',
                'price_origin' => 54990000,
                'is_hot' => true,
                'season' => 'winter',
                'sold' => 85,
                'like_count' => 165,
                'warranty' => 24,
                'tag_slugs' => ['laptopasus', 'gaming'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_881_7.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_881_7.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-rog-zephyrus-g14-trang-2.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-rog-zephyrus-g14-trang-2.png']
                ],
                'description' => 'Laptop gaming mỏng nhẹ đẹp nhất thế giới với màn hình ROG Nebula OLED 3K 120Hz 0.2ms, dải đèn Slash Lighting độc bản và card đồ họa RTX 4070.',
                'content' => '<p><strong>Asus ROG Zephyrus G14</strong> sử dụng vi xử lý AMD Ryzen 9 tích hợp AI Ryzen, khung nhôm CNC nguyên khối chỉ dày 1.59cm và nặng 1.5kg.</p>',
                'variants' => [
                    ['sku' => 'ROG-G14-32-1TB-WHT', 'name' => 'ROG Zephyrus G14 32GB 1TB Trắng', 'price' => 49990000, 'stock' => 15, 'attrs' => ['32GB', '1TB', 'Trắng']],
                ]
            ],
            [
                'name' => 'Dell XPS 13 Plus 9320',
                'brand_name' => 'Dell',
                'catalogue_slug' => 'laptop',
                'price_origin' => 56990000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 65,
                'like_count' => 98,
                'warranty' => 12,
                'tag_slugs' => ['laptopdell', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_945_1_21.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_945_1_21.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/d/e/dell-xps-13-plus-bac.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/d/e/dell-xps-13-plus-bac.png']
                ],
                'description' => 'Biểu tượng thiết kế tương lai của Dell với bàn phím liền mạch zero-lattice, dãy phím chức năng cảm ứng điện dung và touchpad kính vô hình.',
                'content' => '<p><strong>Dell XPS 13 Plus</strong> sở hữu màn hình InfinityEdge 3.5K OLED rực rỡ, Intel Core i7 thế hệ 13 mang lại hiệu năng giải quyết công việc tức thì.</p>',
                'variants' => [
                    ['sku' => 'XPS13-16-512-SIL', 'name' => 'Dell XPS 13 Plus 16GB 512GB Bạc', 'price' => 49990000, 'stock' => 18, 'attrs' => ['16GB', '512GB', 'Bạc']],
                    ['sku' => 'XPS13-16-512-BLK', 'name' => 'Dell XPS 13 Plus 16GB 512GB Graphite', 'price' => 49990000, 'stock' => 15, 'attrs' => ['16GB', '512GB', 'Đen']],
                    ['sku' => 'XPS13-16-1TB-SIL', 'name' => 'Dell XPS 13 Plus 16GB 1TB Bạc', 'price' => 52990000, 'stock' => 12, 'attrs' => ['16GB', '1TB', 'Bạc']],
                    ['sku' => 'XPS13-16-1TB-BLK', 'name' => 'Dell XPS 13 Plus 16GB 1TB Graphite', 'price' => 52990000, 'stock' => 12, 'attrs' => ['16GB', '1TB', 'Đen']],
                    ['sku' => 'XPS13-32-512-SIL', 'name' => 'Dell XPS 13 Plus 32GB 512GB Bạc', 'price' => 53490000, 'stock' => 10, 'attrs' => ['32GB', '512GB', 'Bạc']],
                    ['sku' => 'XPS13-32-512-BLK', 'name' => 'Dell XPS 13 Plus 32GB 512GB Graphite', 'price' => 53490000, 'stock' => 10, 'attrs' => ['32GB', '512GB', 'Đen']],
                    ['sku' => 'XPS13-32-1TB-SIL', 'name' => 'Dell XPS 13 Plus 32GB 1TB Bạc', 'price' => 55490000, 'stock' => 10, 'attrs' => ['32GB', '1TB', 'Bạc']],
                    ['sku' => 'XPS13-32-1TB-BLK', 'name' => 'Dell XPS 13 Plus 32GB 1TB Graphite', 'price' => 55490000, 'stock' => 10, 'attrs' => ['32GB', '1TB', 'Đen']],
                ]
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                'brand_name' => 'Lenovo',
                'catalogue_slug' => 'laptop',
                'price_origin' => 46990000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 92,
                'like_count' => 110,
                'warranty' => 36,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_945_2__3.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_945_2__3.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/l/e/lenovo-thinkpad-x1-carbon-den.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/l/e/lenovo-thinkpad-x1-carbon-den.png']
                ],
                'description' => 'Chuẩn mực laptop doanh nhân toàn cầu chế tác từ sợi carbon siêu bền đạt chuẩn quân đội Mỹ MIL-STD-810H, bàn phím gõ đỉnh cao không đối thủ.',
                'content' => '<p><strong>ThinkPad X1 Carbon Gen 11</strong> nặng chỉ 1.12kg nhưng trang bị đầy đủ cổng kết nối cao cấp: 2 cổng Thunderbolt 4, HDMI, USB-A cùng bảo mật vân tay và nhận diện khuôn mặt IR.</p>',
                'variants' => [
                    ['sku' => 'X1-16-512-BLK', 'name' => 'ThinkPad X1 Carbon 16GB 512GB Đen', 'price' => 41990000, 'stock' => 20, 'attrs' => ['16GB', '512GB', 'Đen']],
                    ['sku' => 'X1-16-1TB-BLK', 'name' => 'ThinkPad X1 Carbon 16GB 1TB Đen', 'price' => 44990000, 'stock' => 15, 'attrs' => ['16GB', '1TB', 'Đen']],
                    ['sku' => 'X1-32-512-BLK', 'name' => 'ThinkPad X1 Carbon 32GB 512GB Đen', 'price' => 45490000, 'stock' => 15, 'attrs' => ['32GB', '512GB', 'Đen']],
                    ['sku' => 'X1-32-1TB-BLK', 'name' => 'ThinkPad X1 Carbon 32GB 1TB Đen', 'price' => 47990000, 'stock' => 10, 'attrs' => ['32GB', '1TB', 'Đen']],
                ]
            ],
            [
                'name' => 'Laptop Asus TUF Gaming A15 FA507',
                'brand_name' => 'Asus',
                'catalogue_slug' => 'laptop',
                'price_origin' => 25990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 340,
                'like_count' => 220,
                'warranty' => 24,
                'tag_slugs' => ['laptopasus', 'gaming'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/l/a/laptop-asus-tuf-gaming-a15-fa507uv-lp090w.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/l/a/laptop-asus-tuf-gaming-a15-fa507uv-lp090w.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-tuf-a15-2.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-tuf-a15-2.png']
                ],
                'description' => 'Chiến thần laptop gaming quốc dân trang bị AMD Ryzen 7 7735HS, card đồ họa NVIDIA GeForce RTX 4060 8GB TGP tối đa 140W và màn hình 144Hz 100% sRGB.',
                'content' => '<p><strong>Asus TUF Gaming A15</strong> đáp ứng tốt mọi tựa game AAA và phần mềm kỹ thuật nặng nhờ hệ thống quạt Arc Flow Fans tản nhiệt mát mẻ bền bỉ.</p>',
                'variants' => [
                    ['sku' => 'TUF-A15-16-512', 'name' => 'Asus TUF A15 16GB 512GB Xám Mecha', 'price' => 22490000, 'stock' => 45, 'attrs' => ['16GB', '512GB', 'Xám Titan']],
                ]
            ],
            [
                'name' => 'HP Envy x360 14 2-in-1',
                'brand_name' => 'HP',
                'catalogue_slug' => 'laptop',
                'price_origin' => 24990000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 145,
                'like_count' => 88,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_744_1_42.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_744_1_42.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/p/hp-envy-bac.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/p/hp-envy-bac.png']
                ],
                'description' => 'Laptop cảm ứng xoay gập 360 độ linh hoạt của HP, màn hình cảm ứng 2.8K OLED tỉ lệ 16:10 tương thích bút stylus viết vẽ chính xác.',
                'content' => '<p><strong>HP Envy x360 14</strong> tích hợp chip Intel Core Ultra thế hệ mới xử lý AI, camera 5MP khử ồn thông minh cho các cuộc họp trực tuyến chuyên nghiệp.</p>',
                'variants' => [
                    ['sku' => 'HP-ENVY-16-512', 'name' => 'HP Envy x360 16GB 512GB Bạc', 'price' => 21990000, 'stock' => 25, 'attrs' => ['16GB', '512GB', 'Bạc']],
                ]
            ],

            // ================= TABLETS =================
            [
                'name' => 'iPad Pro M4 11 inch Wi-Fi',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'tablet',
                'price_origin' => 28990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 220,
                'like_count' => 290,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-pro-m5.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-pro-m5.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-pro-11-m4-bac.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-pro-11-m4-bac.png']
                ],
                'description' => 'Thiết bị mỏng nhất lịch sử Apple chỉ 5.3mm! Sức mạnh quái vật từ chip M4 tiến trình 3nm thứ hai cùng màn hình OLED 2 lớp Ultra Retina XDR.',
                'content' => '<p><strong>iPad Pro M4</strong> tương thích hoàn hảo với Apple Pencil Pro có phản hồi xúc giác và Magic Keyboard mới với bàn rê chuột haptic lớn hơn.</p>',
                'variants' => [
                    ['sku' => 'IPAD-M4-256-BLK', 'name' => 'iPad Pro M4 11 256GB Đen Không Gian', 'price' => 27990000, 'stock' => 35, 'attrs' => ['256GB', 'Đen']],
                    ['sku' => 'IPAD-M4-256-SIL', 'name' => 'iPad Pro M4 11 256GB Bạc', 'price' => 27990000, 'stock' => 30, 'attrs' => ['256GB', 'Bạc']],
                    ['sku' => 'IPAD-M4-512-BLK', 'name' => 'iPad Pro M4 11 512GB Đen Không Gian', 'price' => 33490000, 'stock' => 25, 'attrs' => ['512GB', 'Đen']],
                    ['sku' => 'IPAD-M4-512-SIL', 'name' => 'iPad Pro M4 11 512GB Bạc', 'price' => 33490000, 'stock' => 20, 'attrs' => ['512GB', 'Bạc']],
                ]
            ],
            [
                'name' => 'iPad Air 6 M2 11 inch Wi-Fi',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'tablet',
                'price_origin' => 16990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 310,
                'like_count' => 195,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-air-11-wifi-1.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-air-11-wifi-1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-air-6-xanh.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-air-6-xanh.png']
                ],
                'description' => 'iPad Air thế hệ mới nâng cấp chip Apple M2 mạnh hơn 50%, camera trước chuyển sang cạnh ngang tối ưu gọi video call và học tập online.',
                'content' => '<p><strong>iPad Air 6 M2</strong> mang lại sức mạnh đồ họa đỉnh cao với bộ nhớ trong khởi điểm 128GB gấp đôi thế hệ trước.</p>',
                'variants' => [
                    ['sku' => 'IPAD-AIR6-128-BLU', 'name' => 'iPad Air 6 128GB Xanh Dương', 'price' => 16290000, 'stock' => 50, 'attrs' => ['128GB', 'Xanh Dương']],
                    ['sku' => 'IPAD-AIR6-128-GRY', 'name' => 'iPad Air 6 128GB Xám Không Gian', 'price' => 16290000, 'stock' => 45, 'attrs' => ['128GB', 'Xám Titan']],
                    ['sku' => 'IPAD-AIR6-256-BLU', 'name' => 'iPad Air 6 256GB Xanh Dương', 'price' => 18990000, 'stock' => 35, 'attrs' => ['256GB', 'Xanh Dương']],
                    ['sku' => 'IPAD-AIR6-256-GRY', 'name' => 'iPad Air 6 256GB Xám Không Gian', 'price' => 18990000, 'stock' => 30, 'attrs' => ['256GB', 'Xám Titan']],
                ]
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra 5G',
                'brand_name' => 'Samsung',
                'catalogue_slug' => 'tablet',
                'price_origin' => 32990000,
                'is_hot' => false,
                'season' => 'winter',
                'sold' => 110,
                'like_count' => 140,
                'warranty' => 12,
                'tag_slugs' => ['samsunggalaxy', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-tab-s9-ultra.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/a/samsung-galaxy-tab-s9-ultra.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/a/tab-s9-ultra-den.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/a/tab-s9-ultra-den.png']
                ],
                'description' => 'Máy tính bảng Android đỉnh cao nhất với màn hình siêu lớn 14.6 inch Dynamic AMOLED 2X, chuẩn kháng nước bụi IP68 cả thân máy và bút S-Pen.',
                'content' => '<p><strong>Galaxy Tab S9 Ultra</strong> biến thành máy trạm chuyên nghiệp với chế độ Samsung DeX và hệ thống 4 loa AKG sống động.</p>',
                'variants' => [
                    ['sku' => 'TABS9U-256-GRY', 'name' => 'Galaxy Tab S9 Ultra 256GB Xám', 'price' => 26990000, 'stock' => 20, 'attrs' => ['256GB', 'Xám Titan']],
                    ['sku' => 'TABS9U-512-GRY', 'name' => 'Galaxy Tab S9 Ultra 512GB Xám', 'price' => 30490000, 'stock' => 15, 'attrs' => ['512GB', 'Xám Titan']],
                ]
            ],
            [
                'name' => 'Xiaomi Pad 6',
                'brand_name' => 'Xiaomi',
                'catalogue_slug' => 'tablet',
                'price_origin' => 8990000,
                'is_hot' => true,
                'season' => 'none',
                'sold' => 460,
                'like_count' => 230,
                'warranty' => 18,
                'tag_slugs' => ['giare'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/may-tinh-bang-xiaomi-poco-pad-x1-4.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/may-tinh-bang-xiaomi-poco-pad-x1-4.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-pad-6-xam.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/x/i/xiaomi-pad-6-xam.png']
                ],
                'description' => 'Máy tính bảng cấu hình số 1 tầm giá với màn hình 11 inch 2.8K tần số quét 144Hz 7 mức điều chỉnh, chip Snapdragon 870 và pin 8840mAh.',
                'content' => '<p><strong>Xiaomi Pad 6</strong> hoàn hảo cho nhu cầu xem phim, giải trí và học tập với thân máy kim loại nguyên khối unibody siêu mỏng 6.51mm.</p>',
                'variants' => [
                    ['sku' => 'MIPAD6-8-128-GRY', 'name' => 'Xiaomi Pad 6 8GB 128GB Xám', 'price' => 7990000, 'stock' => 60, 'attrs' => ['8GB', '128GB', 'Xám Titan']],
                    ['sku' => 'MIPAD6-8-128-BLU', 'name' => 'Xiaomi Pad 6 8GB 128GB Xanh', 'price' => 7990000, 'stock' => 50, 'attrs' => ['8GB', '128GB', 'Xanh Dương']],
                    ['sku' => 'MIPAD6-8-256-GRY', 'name' => 'Xiaomi Pad 6 8GB 256GB Xám', 'price' => 8490000, 'stock' => 45, 'attrs' => ['8GB', '256GB', 'Xám Titan']],
                    ['sku' => 'MIPAD6-8-256-BLU', 'name' => 'Xiaomi Pad 6 8GB 256GB Xanh', 'price' => 8490000, 'stock' => 40, 'attrs' => ['8GB', '256GB', 'Xanh Dương']],
                ]
            ],
            [
                'name' => 'iPad 10.9 inch Gen 10',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'tablet',
                'price_origin' => 11490000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 580,
                'like_count' => 310,
                'warranty' => 12,
                'tag_slugs' => ['giare'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-a16-11-inch_10__1.jpg', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-a16-11-inch_10__1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-gen-10-vang.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/p/ipad-gen-10-vang.png']
                ],
                'description' => 'iPad thế hệ 10 thiết kế toàn màn hình Liquid Retina 10.9 inch hiện đại, cổng USB-C tiện lợi và chip A14 Bionic mạnh mẽ.',
                'content' => '<p><strong>iPad Gen 10</strong> hỗ trợ bàn phím Magic Keyboard Folio 2 mảnh và Apple Pencil, là thiết bị học tập và làm việc lý tưởng cho học sinh, sinh viên.</p>',
                'variants' => [
                    ['sku' => 'IPAD-GEN10-64-YEL', 'name' => 'iPad Gen 10 64GB Vàng', 'price' => 9490000, 'stock' => 70, 'attrs' => ['64GB', 'Vàng']],
                    ['sku' => 'IPAD-GEN10-64-SIL', 'name' => 'iPad Gen 10 64GB Bạc', 'price' => 9490000, 'stock' => 60, 'attrs' => ['64GB', 'Bạc']],
                    ['sku' => 'IPAD-GEN10-256-YEL', 'name' => 'iPad Gen 10 256GB Vàng', 'price' => 12990000, 'stock' => 45, 'attrs' => ['256GB', 'Vàng']],
                    ['sku' => 'IPAD-GEN10-256-SIL', 'name' => 'iPad Gen 10 256GB Bạc', 'price' => 12990000, 'stock' => 40, 'attrs' => ['256GB', 'Bạc']],
                ]
            ],

            // ================= AUDIO & WEARABLES =================
            [
                'name' => 'Apple AirPods Pro 2 USB-C',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 6190000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 820,
                'like_count' => 490,
                'warranty' => 12,
                'tag_slugs' => ['tainghe', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/54/315014/tai-nghe-bluetooth-airpods-pro-2nd-gen-usb-c-charge-apple-thumb-1-600x600.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/54/315014/tai-nghe-bluetooth-airpods-pro-2nd-gen-usb-c-charge-apple-thumb-1-600x600.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/i/airpods-pro-2-case.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/i/airpods-pro-2-case.png']
                ],
                'description' => 'Tai nghe chống ồn chủ động đỉnh cao số 1 với chip H2 thông minh, âm thanh không gian cá nhân hóa và hộp sạc chuẩn USB-C chống bụi IP54.',
                'content' => '<p><strong>AirPods Pro 2 USB-C</strong> khử tiếng ồn chủ động gấp 2 lần, tính năng Nhận biết cuộc hội thoại tự động giảm âm lượng khi bạn bắt đầu trò chuyện.</p>',
                'variants' => [
                    ['sku' => 'APP2-USBC', 'name' => 'Apple AirPods Pro 2 MagSafe USB-C', 'price' => 5390000, 'stock' => 120, 'attrs' => ['Trắng']],
                ]
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'brand_name' => 'Sony',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 8490000,
                'is_hot' => true,
                'season' => 'winter',
                'sold' => 240,
                'like_count' => 310,
                'warranty' => 12,
                'tag_slugs' => ['tainghe', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/54/327510/tai-nghe-bluetooth-chup-tai-sony-wh-ult900n-den-1.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/54/327510/tai-nghe-bluetooth-chup-tai-sony-wh-ult900n-den-1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/o/sony-wh-1000xm5-bac.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/s/o/sony-wh-1000xm5-bac.png']
                ],
                'description' => 'Tai nghe chụp tai chống ồn hàng đầu thế giới của Sony trang bị 2 bộ vi xử lý và 8 micrô kiểm soát tạp âm tuyệt đối, đệm da mềm ôm trọn tai.',
                'content' => '<p><strong>Sony WH-1000XM5</strong> hỗ trợ codec âm thanh không dây độ phân giải cao Hi-Res Audio Wireless LDAC cùng thời lượng pin liên tục lên đến 30 tiếng.</p>',
                'variants' => [
                    ['sku' => 'XM5-BLK', 'name' => 'Sony WH-1000XM5 Đen', 'price' => 6990000, 'stock' => 30, 'attrs' => ['Đen']],
                    ['sku' => 'XM5-SIL', 'name' => 'Sony WH-1000XM5 Bạc', 'price' => 6990000, 'stock' => 25, 'attrs' => ['Bạc']],
                ]
            ],
            [
                'name' => 'Loa Bluetooth Marshall Emberton II',
                'brand_name' => 'Marshall',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 4990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 310,
                'like_count' => 280,
                'warranty' => 12,
                'tag_slugs' => ['loa'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/2162/327436/loa-bluetooth-jbl-clip-5-den-1.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/2162/327436/loa-bluetooth-jbl-clip-5-den-1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/marshall-emberton-ii-kem.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/marshall-emberton-ii-kem.png']
                ],
                'description' => 'Âm thanh đa hướng True Stereophonic 360 độ huyền thoại của Marshall trong thân hình nhỏ gọn di động, kháng nước bụi IP67 chuẩn dã ngoại.',
                'content' => '<p><strong>Marshall Emberton II</strong> chơi nhạc liên tục hơn 30 giờ chỉ với một lần sạc, kết nối chế độ Stack Mode ghép nhiều loa cùng lúc bùng nổ bữa tiệc.</p>',
                'variants' => [
                    ['sku' => 'EMBERTON-II-BLK', 'name' => 'Marshall Emberton II Đen Viền Vàng', 'price' => 3990000, 'stock' => 40, 'attrs' => ['Đen']],
                    ['sku' => 'EMBERTON-II-CRM', 'name' => 'Marshall Emberton II Kem Vintage', 'price' => 3990000, 'stock' => 25, 'attrs' => ['Vàng']],
                ]
            ],
            [
                'name' => 'Loa Bluetooth JBL Charge 5',
                'brand_name' => 'JBL',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 3990000,
                'is_hot' => false,
                'season' => 'summer',
                'sold' => 450,
                'like_count' => 190,
                'warranty' => 12,
                'tag_slugs' => ['loa'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/2162/251230/bluetooth-jbl-charge-5-1-1.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/2162/251230/bluetooth-jbl-charge-5-1-1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/j/b/jbl-charge-5-camo.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/j/b/jbl-charge-5-camo.png']
                ],
                'description' => 'Âm trầm JBL Pro Sound đầy nội lực với củ loa racetrack độc lập và loa tweeter riêng biệt, kiêm sạc dự phòng sạc ngược lại cho điện thoại.',
                'content' => '<p><strong>JBL Charge 5</strong> đạt chuẩn kháng nước IP67 cùng thời lượng pin 20 tiếng, luôn sẵn sàng cho mọi hành trình biển đảo hay cắm trại ngoài trời.</p>',
                'variants' => [
                    ['sku' => 'CHARGE5-BLK', 'name' => 'JBL Charge 5 Đen', 'price' => 3290000, 'stock' => 50, 'attrs' => ['Đen']],
                    ['sku' => 'CHARGE5-CAM', 'name' => 'JBL Charge 5 Rằn Ri Camo', 'price' => 3290000, 'stock' => 30, 'attrs' => ['Xanh Lá']],
                ]
            ],
            [
                'name' => 'Apple Watch Ultra 2 GPS + Cellular 49mm',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dong-ho-thong-minh',
                'price_origin' => 21990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 160,
                'like_count' => 220,
                'warranty' => 12,
                'tag_slugs' => ['flagship'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/7077/344764/apple-watch-ultra-3-gps-cellular-49mm-vien-titanium-day-ocean-tu-nhien.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/7077/344764/apple-watch-ultra-3-gps-cellular-49mm-vien-titanium-day-ocean-tu-nhien.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/p/apple-watch-ultra-2-ocean.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/p/apple-watch-ultra-2-ocean.png']
                ],
                'description' => 'Đồng hồ thể thao chuyên nghiệp đỉnh cao với vỏ Titanium 49mm chống ăn mòn, màn hình sáng kỷ lục 3000 nits, chip S9 SiP và cử chỉ Double Tap.',
                'content' => '<p><strong>Apple Watch Ultra 2</strong> đồng hành cùng vận động viên lặn sâu 40m, leo núi với GPS kép tần số chuẩn xác và pin lên đến 72 giờ ở chế độ tiết kiệm năng lượng.</p>',
                'variants' => [
                    ['sku' => 'AWU2-ALP-BLU', 'name' => 'Apple Watch Ultra 2 Dây Alpine Xanh', 'price' => 20490000, 'stock' => 25, 'attrs' => ['Xanh Dương']],
                    ['sku' => 'AWU2-OCN-WHT', 'name' => 'Apple Watch Ultra 2 Dây Ocean Trắng', 'price' => 20490000, 'stock' => 20, 'attrs' => ['Trắng']],
                ]
            ],

            // ================= ACCESSORIES & GAMING GEARS =================
            [
                'name' => 'Chuột không dây Logitech MX Master 3S',
                'brand_name' => 'Logitech',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 2490000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 730,
                'like_count' => 410,
                'warranty' => 12,
                'tag_slugs' => ['phukien', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-khong-day-logitech-mx-master-3s.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-khong-day-logitech-mx-master-3s.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-logitech-mx-master-3s-xam.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-logitech-mx-master-3s-xam.png']
                ],
                'description' => 'Chuột công thái học cao cấp tốt nhất cho lập trình viên và nhà thiết kế. Cảm biến 8000 DPI di chuyển trên mọi bề mặt kính, con lăn điện từ MagSpeed cuộn 1000 dòng/giây.',
                'content' => '<p><strong>Logitech MX Master 3S</strong> nâng cấp phím bấm Quiet Click giảm 90% tiếng ồn, kết nối 3 thiết bị và chuyển file mượt mà qua Logitech Flow.</p>',
                'variants' => [
                    ['sku' => 'MXM3S-GRY', 'name' => 'Logitech MX Master 3S Đen Graphite', 'price' => 2190000, 'stock' => 90, 'attrs' => ['Đen']],
                    ['sku' => 'MXM3S-WHT', 'name' => 'Logitech MX Master 3S Xám Pale Gray', 'price' => 2190000, 'stock' => 60, 'attrs' => ['Trắng']],
                ]
            ],
            [
                'name' => 'Bàn phím cơ không dây Asus ROG Azoth 75%',
                'brand_name' => 'Asus',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 6490000,
                'is_hot' => true,
                'season' => 'winter',
                'sold' => 190,
                'like_count' => 260,
                'warranty' => 24,
                'tag_slugs' => ['gaming', 'phukien'],
                'album' => [
                    ['url' => 'https://cdn.tgdd.vn/Products/Images/4547/279466/co-day-gaming-asus-tuf-k1-1.jpg', 'path' => 'https://cdn.tgdd.vn/Products/Images/4547/279466/co-day-gaming-asus-tuf-k1-1.jpg'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-rog-azoth-led.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-rog-azoth-led.png']
                ],
                'description' => 'Bàn phím custom cơ không dây cao cấp với màn hình OLED hiển thị thông số và núm xoay ba chiều, gasket mount đệm silicon cho âm gõ êm ái đầm chắc.',
                'content' => '<p><strong>ROG Azoth 75%</strong> hỗ trợ hotswap switch, kết nối SpeedNova 2.4GHz độ trễ siêu thấp và đi kèm bộ kit lube switch chuyên nghiệp.</p>',
                'variants' => [
                    ['sku' => 'AZOTH-RED', 'name' => 'Asus ROG Azoth NX Red Switch', 'price' => 5490000, 'stock' => 25, 'attrs' => ['Đen']],
                    ['sku' => 'AZOTH-BLU', 'name' => 'Asus ROG Azoth NX Blue Switch', 'price' => 5490000, 'stock' => 20, 'attrs' => ['Đen']],
                ]
            ],
            [
                'name' => 'Củ sạc nhanh Anker 735 GaNPrime 65W 3 cổng',
                'brand_name' => 'Anker',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 1490000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 980,
                'like_count' => 520,
                'warranty' => 18,
                'tag_slugs' => ['phukien'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/f/r/frame_511_5_.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/f/r/frame_511_5_.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/u/cu-sac-anker-65w-2.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/u/cu-sac-anker-65w-2.png']
                ],
                'description' => 'Công nghệ GaNPrime độc quyền sạc nhanh cùng lúc cho MacBook, iPhone và tai nghe. Thiết kế nhỏ gọn hơn 53% so với củ sạc 67W thông thường.',
                'content' => '<p><strong>Anker GaNPrime 65W (A2668)</strong> phân bổ công suất thông minh PowerIQ 4.0 và cảm biến kiểm soát nhiệt độ ActiveShield 2.0 an toàn tuyệt đối.</p>',
                'variants' => [
                    ['sku' => 'ANK-65W-BLK', 'name' => 'Củ sạc Anker GaNPrime 65W Đen', 'price' => 1090000, 'stock' => 150, 'attrs' => ['Đen']],
                    ['sku' => 'ANK-65W-SIL', 'name' => 'Củ sạc Anker GaNPrime 65W Bạc', 'price' => 1090000, 'stock' => 100, 'attrs' => ['Bạc']],
                ]
            ],
            [
                'name' => 'Pin sạc dự phòng Baseus Blade 100W 20000mAh',
                'brand_name' => 'Baseus',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 1890000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 620,
                'like_count' => 290,
                'warranty' => 12,
                'tag_slugs' => ['phukien'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/pin-du-phong-baseus-enercore-cr11-kem-cap-rut-gon-20000mah-67w.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/pin-du-phong-baseus-enercore-cr11-kem-cap-rut-gon-20000mah-67w.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/baseus-blade-100w-man-hinh.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/baseus-blade-100w-man-hinh.png']
                ],
                'description' => 'Pin dự phòng siêu mỏng chỉ 18mm công suất khủng 100W Power Delivery sạc đầy laptop, màn hình LED kỹ thuật số hiển thị điện áp, công suất và thời gian còn lại.',
                'content' => '<p><strong>Baseus Blade 100W</strong> được cấp phép mang lên máy bay, 4 cổng sạc ra vào tiện lợi giúp nạp đầy năng lượng cho mọi thiết bị di động.</p>',
                'variants' => [
                    ['sku' => 'BASEUS-BLADE-100W', 'name' => 'Pin dự phòng Baseus Blade 100W 20000mAh', 'price' => 1390000, 'stock' => 80, 'attrs' => ['Đen']],
                ]
            ],
            [
                'name' => 'Hub chuyển đổi Ugreen USB-C 9 trong 1 đa năng',
                'brand_name' => 'Ugreen',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 1290000,
                'is_hot' => false,
                'season' => 'none',
                'sold' => 410,
                'like_count' => 180,
                'warranty' => 18,
                'tag_slugs' => ['phukien'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/pin-du-phong-ugreen-10000mah-55w-1c1a-qc-3-0-man-hinh-tich-hop-cap-type-c-pb727-75701b.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/p/i/pin-du-phong-ugreen-10000mah-55w-1c1a-qc-3-0-man-hinh-tich-hop-cap-type-c-pb727-75701b.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/u/g/ugreen-hub-9in1-cong.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/u/g/ugreen-hub-9in1-cong.png']
                ],
                'description' => 'Mở rộng cổng kết nối cho MacBook và laptop với cổng HDMI 4K@60Hz, Gigabit Ethernet RJ45 1000Mbps, khe thẻ SD/TF và sạc passthrough 100W.',
                'content' => '<p><strong>Ugreen USB-C 9 in 1</strong> vỏ nhôm nguyên khối tản nhiệt tốt, bảo vệ quá dòng và truyền dữ liệu tốc độ 5Gbps ổn định.</p>',
                'variants' => [
                    ['sku' => 'UGREEN-HUB-9IN1', 'name' => 'Hub Ugreen USB-C 9 trong 1 Xám', 'price' => 990000, 'stock' => 95, 'attrs' => ['Xám Titan']],
                ]
            ],
            [
                'name' => 'Màn hình đồ họa Asus ProArt PA278CV 27 inch 2K IPS 75Hz',
                'brand_name' => 'Asus',
                'catalogue_slug' => 'phu-kien',
                'price_origin' => 9990000,
                'is_hot' => true,
                'season' => 'summer',
                'sold' => 130,
                'like_count' => 240,
                'warranty' => 36,
                'tag_slugs' => ['phukien', 'flagship'],
                'album' => [
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/0/1/01_3.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/0/1/01_3.png'],
                    ['url' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-proart-pa278cv-mat-sau.png', 'path' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/a/s/asus-proart-pa278cv-mat-sau.png']
                ],
                'description' => 'Màn hình chuyên nghiệp chuẩn đồ họa được cân màu sẵn tại nhà máy Calman Verified, độ phủ màu 100% sRGB, 100% Rec. 709 và cổng kết nối USB-C sạc 65W.',
                'content' => '<p><strong>Asus ProArt PA278CV</strong> sở hữu độ sai lệch màu Delta E < 2 đảm bảo độ chính xác màu tuyệt đối cho thiết kế đồ họa, nhiếp ảnh và in ấn.</p>',
                'variants' => [
                    ['sku' => 'PROART-PA278CV', 'name' => 'Màn hình Asus ProArt PA278CV 27 inch 2K', 'price' => 8990000, 'stock' => 20, 'attrs' => ['Đen']],
                ]
            ],
        ];

        // 7. Thực hiện chèn từng sản phẩm và đồng bộ quan hệ
        foreach ($products as $idx => $prodData) {
            DB::transaction(function () use ($prodData, $idx, $attrCatGeneral, $attrCatColor, $attrCatStorage, $attrCatRam) {
                $brand = Brand::where('name', $prodData['brand_name'])->first();
                $catalogue = ProductCatalogue::where('slug', $prodData['catalogue_slug'])->first();

                // Lấy tag IDs
                $tagIds = [];
                $mainTagId = 0;
                if (!empty($prodData['tag_slugs'])) {
                    $tags = ProductTag::whereIn('slug', $prodData['tag_slugs'])->get();
                    $tagIds = $tags->pluck('id')->toArray();
                    $mainTagId = $tagIds[0] ?? 0;
                }

                $product = Product::create([
                    'name' => $prodData['name'],
                    'slug' => Str::slug($prodData['name']),
                    'sku' => $prodData['variants'][0]['sku'] . '-MASTER',
                    'description' => $prodData['description'],
                    'content' => $prodData['content'],
                    'price_origin' => $prodData['price_origin'],
                    'album' => $prodData['album'],
                    'sort_order' => $idx + 1,
                    'product_catalogue_id' => $catalogue ? $catalogue->id : 1,
                    'brand_id' => $brand ? $brand->id : null,
                    'tag_id' => $mainTagId,
                    'is_hot' => $prodData['is_hot'],
                    'season' => $prodData['season'],
                    'warranty' => $prodData['warranty'],
                    'sold' => $prodData['sold'],
                    'like_count' => $prodData['like_count'],
                    'user_id' => 1,
                    'publish' => 2, // Đã xuất bản
                ]);

                // Sync quan hệ catalogue & tags
                if ($catalogue) {
                    $product->product_catalogues()->sync([$catalogue->id]);
                }
                if (!empty($tagIds)) {
                    $product->product_tags()->sync($tagIds);
                }

                // Chèn các variants và thuộc tính
                foreach ($prodData['variants'] as $variantData) {
                    $variant = ProductVariant::create([
                        'name' => $variantData['name'],
                        'code' => $variantData['sku'],
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'product_id' => $product->id,
                        'user_id' => 1,
                        'publish' => 2, // Đã xuất bản
                    ]);

                    $attrIds = [];
                    $attrData = [];

                    foreach ($variantData['attrs'] as $attrName) {
                        // Xác định nhóm thuộc tính
                        $cat = $attrCatGeneral;
                        if (in_array($attrName, ['8GB', '16GB', '18GB', '32GB', '36GB'])) {
                            $cat = $attrCatRam;
                        } elseif (in_array($attrName, ['64GB', '128GB', '256GB', '512GB', '1TB', '2TB'])) {
                            $cat = $attrCatStorage;
                        } elseif (in_array($attrName, ['Titan Sa Mạc', 'Titan Tự Nhiên', 'Titan Đen', 'Đen Titan', 'Xám Titan', 'Đen', 'Trắng', 'Bạc', 'Vàng', 'Hồng', 'Xanh Dương', 'Xanh Lá'])) {
                            $cat = $attrCatColor;
                        }

                        $attr = Attribute::firstOrCreate(
                            ['slug' => Str::slug($attrName)],
                            [
                                'name' => $attrName,
                                'attribute_catalogue_id' => $cat->id,
                                'publish' => 2,
                                'user_id' => 1
                            ]
                        );

                        if ($attr->attribute_catalogue_id !== $cat->id) {
                            $attr->update(['attribute_catalogue_id' => $cat->id]);
                        }

                        $attrIds[] = $attr->id;
                        $attrData[$cat->name] = [
                            'id' => $attr->id,
                            'name' => $attr->name,
                        ];
                    }

                    sort($attrIds);
                    $variantCode = implode('_', $attrIds);

                    $variant->variant_attributes()->sync($attrIds);
                    $variant->update([
                        'attribute_data' => $attrData,
                        'code' => $variantCode ?: $variantData['sku'],
                    ]);
                }
            });
        }
    }
}
