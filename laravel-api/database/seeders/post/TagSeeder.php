<?php

namespace Database\Seeders\Post;

use Illuminate\Database\Seeder;
use App\Models\PostTag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "iPhone 15 Series",
                "slug" => "iphone-15-series",
                "description" => "Các bài viết liên quan đến dòng điện thoại iPhone 15 mới nhất của Apple.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Samsung Galaxy",
                "slug" => "samsung-galaxy",
                "description" => "Tin tức và đánh giá về hệ sinh thái Samsung Galaxy.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "MacBook Pro",
                "slug" => "macbook-pro",
                "description" => "Thông tin về các dòng máy tính xách tay chuyên nghiệp của Apple.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Gaming Gear",
                "slug" => "gaming-gear",
                "description" => "Chuột, bàn phím, tai nghe và các thiết bị dành cho game thủ.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Thủ thuật iOS",
                "slug" => "thu-thuat-ios",
                "description" => "Mẹo hay và hướng dẫn sử dụng hệ điều hành iOS hiệu quả.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Android Tips",
                "slug" => "android-tips",
                "description" => "Tùy biến và tối ưu hóa điện thoại Android của bạn.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Review Camera",
                "slug" => "review-camera",
                "description" => "Đánh giá chi tiết khả năng chụp ảnh, quay phim của các thiết bị.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Sale Sốc",
                "slug" => "sale-soc",
                "description" => "Tổng hợp các deal giảm giá mạnh nhất trong ngày.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Tin Rò Rỉ",
                "slug" => "tin-ro-ri",
                "description" => "Thông tin leak về các sản phẩm công nghệ chưa ra mắt.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "AI & ChatGPT",
                "slug" => "ai-chatgpt",
                "description" => "Xu hướng trí tuệ nhân tạo và các ứng dụng thực tế.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Smart Home",
                "slug" => "smart-home",
                "description" => "Giải pháp nhà thông minh, IoT và tự động hóa.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Xe Điện",
                "slug" => "xe-dien",
                "description" => "Công nghệ xe điện, pin và trạm sạc.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Blockchain",
                "slug" => "blockchain",
                "description" => "Kiến thức về công nghệ chuỗi khối và ứng dụng.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Snapdragon",
                "slug" => "snapdragon",
                "description" => "Tin tức về vi xử lý di động của Qualcomm.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Apple M3",
                "slug" => "apple-m3",
                "description" => "Sức mạnh của con chip Apple Silicon thế hệ mới.",
                "is_hot" => 1,
                "publish" => 2
            ],
            [
                "name" => "Windows 11",
                "slug" => "windows-11",
                "description" => "Cập nhật, thủ thuật và lỗi thường gặp trên Windows 11.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Phụ Kiện Zin",
                "slug" => "phu-kien-zin",
                "description" => "Phân biệt phụ kiện chính hãng và hàng fake.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Tai Nghe Chống Ồn",
                "slug" => "tai-nghe-chong-on",
                "description" => "Review các mẫu tai nghe ANC tốt nhất thị trường.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Pin Sạc Nhanh",
                "slug" => "pin-sac-nhanh",
                "description" => "Công nghệ sạc nhanh, sạc dự phòng và tuổi thọ pin.",
                "is_hot" => 0,
                "publish" => 2
            ],
            [
                "name" => "Màn Hình 144Hz",
                "slug" => "man-hinh-144hz",
                "description" => "Trải nghiệm màn hình tần số quét cao cho game thủ.",
                "is_hot" => 0,
                "publish" => 2
            ]
        ];

        foreach ($data as $item) {
            PostTag::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'publish' => $item['publish'],
                'is_hot' => (bool)$item['is_hot'],
                'user_id' => 1,
            ]);
        }
    }
}
