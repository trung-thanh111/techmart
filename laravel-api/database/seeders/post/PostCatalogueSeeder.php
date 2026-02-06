<?php

namespace Database\Seeders\Post;

use Illuminate\Database\Seeder;
use App\Models\PostCatalogue; // Ensure this model exists and is imported correctly

class PostCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Tin tức công nghệ",
                "parent_id" => 0,
                "slug" => "tin-tuc-cong-nghe",
                "description" => "Cập nhật những tin tức mới nhất về thế giới công nghệ, ra mắt sản phẩm và xu hướng thị trường.",
                "image" => "/images/catalogues/news-tech.jpg",
                "order" => 1,
                "publish" => 2,
                "is_hot" => 1, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Tin tức công nghệ mới nhất | TechMart",
                "meta_keyword" => "tin công nghệ, tin tức it, tin mới techmart",
                "meta_description" => "Đọc tin tức công nghệ mới nhất trong ngày tại TechMart."
            ],
            [
                "name" => "Thủ thuật & Hướng dẫn",
                "parent_id" => 0,
                "slug" => "thu-thuat-huong-dan",
                "description" => "Chia sẻ mẹo hay, thủ thuật sử dụng thiết bị và hướng dẫn khắc phục lỗi phần mềm, phần cứng.",
                "image" => "/images/catalogues/tips-tricks.jpg",
                "order" => 3,
                "publish" => 2,
                "is_hot" => 0,
                "user_id" => 1,
                "meta_title" => "Thủ thuật công nghệ và Hướng dẫn sử dụng",
                "meta_keyword" => "thủ thuật ios, thủ thuật android, hướng dẫn sử dụng",
                "meta_description" => "Kho tàng thủ thuật công nghệ và hướng dẫn sử dụng thiết bị hiệu quả."
            ],
            [
                "name" => "Đánh giá sản phẩm",
                "parent_id" => 0,
                "slug" => "danh-gia-san-pham",
                "description" => "Các bài review, trên tay và đánh giá chi tiết các sản phẩm điện thoại, laptop, phụ kiện mới nhất.",
                "image" => "/images/catalogues/reviews.jpg",
                "order" => 2,
                "publish" => 2,
                "is_hot" => 1, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Đánh giá sản phẩm công nghệ chi tiết",
                "meta_keyword" => "review điện thoại, đánh giá laptop, review công nghệ",
                "meta_description" => "Chuyên mục đánh giá, review sản phẩm công nghệ khách quan và chi tiết."
            ],
            [
                "name" => "Khuyến mãi & Sự kiện",
                "parent_id" => 0,
                "slug" => "khuyen-mai-su-kien",
                "description" => "Tổng hợp các chương trình khuyến mãi, giảm giá sốc và các sự kiện nổi bật tại TechMart.",
                "image" => "/images/catalogues/promotions.jpg",
                "order" => 4,
                "publish" => 2,
                "is_hot" => 1, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Tin khuyến mãi và Sự kiện TechMart",
                "meta_keyword" => "khuyến mãi techmart, săn sale công nghệ, sự kiện ra mắt",
                "meta_description" => "Cập nhật các chương trình khuyến mãi hấp dẫn nhất tại TechMart."
            ],
            [
                "name" => "Tư vấn chọn mua",
                "parent_id" => 0,
                "slug" => "tu-van-chon-mua",
                "description" => "Giúp bạn lựa chọn sản phẩm phù hợp nhất với nhu cầu và túi tiền thông qua các bài so sánh, top list.",
                "image" => "/images/catalogues/buying-guide.jpg",
                "order" => 5,
                "publish" => 2,
                "is_hot" => 0, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Tư vấn chọn mua điện thoại, laptop",
                "meta_keyword" => "tư vấn mua laptop, chọn mua điện thoại, top sản phẩm",
                "meta_description" => "Chuyên gia TechMart tư vấn chọn mua sản phẩm công nghệ tốt nhất."
            ],
            [
                "name" => "Điện thoại thông minh",
                "parent_id" => 2,
                "slug" => "chuyen-muc-dien-thoai",
                "description" => "Chuyên sâu về các dòng điện thoại thông minh, so sánh camera, hiệu năng các flagship.",
                "image" => "/images/catalogues/smartphones.jpg",
                "order" => 6,
                "publish" => 2,
                "is_hot" => 0, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Tin tức và đánh giá Điện thoại thông minh",
                "meta_keyword" => "iphone, samsung, xiaomi, tin tức điện thoại",
                "meta_description" => "Thông tin chuyên sâu về thị trường smartphone sôi động."
            ],
            [
                "name" => "Phụ kiện & Đồ chơi",
                "parent_id" => 0,
                "slug" => "phu-kien-do-choi",
                "description" => "Khám phá thế giới phụ kiện công nghệ, tai nghe, loa, bàn phím và các món đồ chơi setup độc đáo.",
                "image" => "/images/catalogues/accessories.jpg",
                "order" => 8,
                "publish" => 2,
                "is_hot" => 0, // Fixed syntax
                "user_id" => 1,
                "meta_title" => "Review Phụ kiện và Đồ chơi công nghệ",
                "meta_keyword" => "tai nghe, loa bluetooth, setup bàn làm việc, phụ kiện",
                "meta_description" => "Review chân thực các món phụ kiện công nghệ hot."
            ]
        ];

        foreach ($data as $item) {
            PostCatalogue::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'image' => $item['image'],
                'order' => $item['order'],
                'publish' => $item['publish'],
                'is_hot' => (bool)$item['is_hot'],
                'parent_id' => $item['parent_id'],
                'user_id' => $item['user_id'],
                'meta_title' => $item['meta_title'],
                'meta_keyword' => $item['meta_keyword'],
                'meta_description' => $item['meta_description'],
                'level' => ($item['parent_id'] == 0) ? 1 : 2,
            ]);
        }
    }
}
