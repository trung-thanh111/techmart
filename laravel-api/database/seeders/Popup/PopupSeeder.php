<?php

namespace Database\Seeders\Popup;

use App\Models\Popup;
use Illuminate\Database\Seeder;

class PopupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $popups = [
            [
                'title' => 'Siêu Hội Công Nghệ TechMart 2026',
                'slug' => 'sieu-hoi-cong-nghe-techmart-2026',
                'type' => 'promotion',
                'content' => '<p>Chào mừng bạn đến với TechMart! Nhập mã <strong>TECHMART10</strong> để được giảm ngay 10% cho đơn hàng đầu tiên từ 200.000đ.</p>',
                'image' => '/images/promotions/promo-1.webp',
                'link' => '/shop',
                'button_text' => 'Khám phá ngay',
                'form_html' => null,
                'show_delay' => 1500,
                'priority' => true,
                'publish' => 2,
            ],
        ];

        foreach ($popups as $popup) {
            Popup::updateOrCreate(
                ['slug' => $popup['slug']],
                $popup
            );
        }
    }
}
