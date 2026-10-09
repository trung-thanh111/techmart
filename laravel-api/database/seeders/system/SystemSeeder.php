<?php

namespace Database\Seeders\System;

use App\Models\System;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'keyword' => 'privacy_policy',
                'content' => '<h2>Chính sách bảo mật TechMart</h2><p>TechMart cam kết bảo vệ tuyệt đối thông tin cá nhân của khách hàng. Chúng tôi thu thập thông tin chỉ nhằm mục đích xử lý đơn hàng và chăm sóc khách hàng. Mọi thông tin thanh toán đều được mã hóa bằng chuẩn bảo mật SSL cao cấp.</p>',
                'publish' => 2,
                'user_id' => 1,
            ],
            [
                'keyword' => 'warranty_policy',
                'content' => '<h2>Chính sách bảo hành chính hãng</h2><p>Tất cả sản phẩm tại TechMart được bảo hành từ 12 đến 24 tháng theo đúng tiêu chuẩn của nhà sản xuất (Apple, Samsung, Sony, Dell, v.v.). Khách hàng có thể mang máy đến bất kỳ trung tâm bảo hành hoặc chi nhánh TechMart trên toàn quốc.</p>',
                'publish' => 2,
                'user_id' => 1,
            ],
            [
                'keyword' => 'delivery_policy',
                'content' => '<h2>Chính sách vận chuyển & giao hàng</h2><p>Miễn phí giao hàng toàn quốc cho đơn hàng từ 1.000.000đ. Đơn hàng nội thành Hà Nội và TP.HCM được giao siêu tốc trong vòng 2-4 giờ. Các tỉnh thành khác thời gian giao hàng từ 1-3 ngày làm việc.</p>',
                'publish' => 2,
                'user_id' => 1,
            ],
            [
                'keyword' => 'return_policy',
                'content' => '<h2>Chính sách đổi trả & hoàn tiền</h2><p>TechMart áp dụng chính sách 1 đổi 1 trong vòng 30 ngày đầu nếu phát sinh lỗi kỹ thuật từ nhà sản xuất. Hoàn tiền 100% nếu sản phẩm không đúng mô tả hoặc phát hiện hàng không chính hãng.</p>',
                'publish' => 2,
                'user_id' => 1,
            ],
        ];

        foreach ($policies as $item) {
            System::firstOrCreate(
                ['keyword' => $item['keyword']],
                $item
            );
        }

        $this->command->info('System policies seeded successfully!');
    }
}
