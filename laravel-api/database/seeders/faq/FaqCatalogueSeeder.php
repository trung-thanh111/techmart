<?php

namespace Database\Seeders\Faq;

use Illuminate\Database\Seeder;
use App\Models\FaqCatalogue;
use Illuminate\Support\Str;

class FaqCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Sản phẩm",
                "description" => "Các câu hỏi chung về sản phẩm, tính năng, dịch vụ và catalog của cửa hàng",
                "image" => null,
                "is_featured" => 1,
                "publish" => 2
            ],
            [
                "name" => "Đặt hàng và thanh toán",
                "description" => "Thông tin về quy trình đặt hàng, các phương thức thanh toán và xử lý đơn hàng",
                "image" => null,
                "is_featured" => 1,
                "publish" => 2
            ],
            [
                "name" => "Giao hàng và vận chuyển",
                "description" => "Câu hỏi về chính sách giao hàng, thời gian và phí vận chuyển",
                "image" => null,
                "is_featured" => 1,
                "publish" => 2
            ],
            [
                "name" => "Bảo hành và hỗ trợ",
                "description" => "Các câu hỏi về chính sách bảo hành, dịch vụ hỗ trợ kỹ thuật và sửa chữa",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Đổi trả và hoàn tiền",
                "description" => "Hướng dẫn về quy trình đổi trả sản phẩm và chính sách hoàn tiền",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Tài khoản khách hàng",
                "description" => "Thông tin về đăng ký, quản lý tài khoản và quyền lợi thành viên",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Khuyến mãi và ưu đãi",
                "description" => "Các câu hỏi về chương trình khuyến mãi, mã giảm giá và tích điểm",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Bảo mật và chính sách",
                "description" => "Thông tin về bảo mật dữ liệu, chính sách riêng tư và điều khoản sử dụng",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Hướng dẫn sử dụng",
                "description" => "Các hướng dẫn về cách sử dụng website, tìm kiếm sản phẩm và mua hàng online",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ],
            [
                "name" => "Liên hệ và hỗ trợ",
                "description" => "Thông tin liên hệ, giờ làm việc và các kênh hỗ trợ khách hàng",
                "image" => null,
                "is_featured" => 0,
                "publish" => 2
            ]
        ];

        foreach ($data as $item) {
            FaqCatalogue::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'image' => $item['image'],
                'is_hot' => (bool)$item['is_featured'], // Map is_featured to is_hot
                'publish' => $item['publish'],
                'user_id' => 1,
            ]);
        }
    }
}
