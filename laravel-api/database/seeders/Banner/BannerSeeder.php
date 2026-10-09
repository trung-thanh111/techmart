<?php

namespace Database\Seeders\Banner;

use App\Models\Banner;
use App\Models\BannerPage;
use App\Models\BannerPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Banner Pages
        $pages = [
            ['name' => 'Trang chủ', 'slug' => 'trang-chu', 'code' => 'home'],
            ['name' => 'Giới thiệu', 'slug' => 'gioi-thieu', 'code' => 'about'],
            ['name' => 'Tin tức', 'slug' => 'tin-tuc', 'code' => 'blog'],
            ['name' => 'Cửa hàng', 'slug' => 'cua-hang', 'code' => 'shop'],
            ['name' => 'Liên hệ', 'slug' => 'lien-he', 'code' => 'contact'],
        ];

        $pageModels = [];
        foreach ($pages as $p) {
            $pageModels[$p['code']] = BannerPage::firstOrCreate(
                ['code' => $p['code']],
                [
                    'name' => $p['name'],
                    'slug' => $p['slug'],
                    'publish' => 2,
                ]
            );
        }

        // 2. Seed Banner Positions
        $positions = [
            ['name' => 'Slider chính', 'slug' => 'slider-chinh', 'code' => 'main_slider'],
            ['name' => 'Banner phải', 'slug' => 'banner-phai', 'code' => 'right_vertical'],
            ['name' => 'Banner giới thiệu', 'slug' => 'banner-gioi-thieu', 'code' => 'intro_banner'],
            ['name' => 'Banner mùa vụ', 'slug' => 'banner-mua-vu', 'code' => 'seasonal_banner'],
        ];

        $posModels = [];
        foreach ($positions as $pos) {
            $posModels[$pos['code']] = BannerPosition::firstOrCreate(
                ['code' => $pos['code']],
                [
                    'name' => $pos['name'],
                    'slug' => $pos['slug'],
                    'publish' => 2,
                ]
            );
        }

        // 3. Seed Banners
        $homePageId = $pageModels['home']->id;
        $shopPageId = $pageModels['shop']->id;

        $mainPosId = $posModels['main_slider']->id;
        $rightPosId = $posModels['right_vertical']->id;
        $introPosId = $posModels['intro_banner']->id;
        $seasonPosId = $posModels['seasonal_banner']->id;

        $banners = [
            [
                'name' => 'Hero Slider Trang Chủ',
                'slug' => 'hero-slider-trang-chu',
                'album' => [
                    '/images/slide/hero-slide-left-1.webp',
                    '/images/slide/hero-slide-left-2.webp',
                    '/images/slide/hero-slide-left-3.webp',
                ],
                'page_id' => $homePageId,
                'position_id' => $mainPosId,
                'link_url' => '/shop',
                'order' => 1,
                'is_hot' => true,
                'publish' => 2,
            ],
            [
                'name' => 'Banner Dọc Phải Trang Chủ',
                'slug' => 'banner-doc-phai-trang-chu',
                'album' => [
                    '/images/slide/hero-slide-right-1.webp',
                    '/images/slide/hero-slide-right-2.webp',
                    '/images/slide/slide_hero_r_3.png',
                ],
                'page_id' => $homePageId,
                'position_id' => $rightPosId,
                'link_url' => '/shop',
                'order' => 1,
                'is_hot' => true,
                'publish' => 2,
            ],
            [
                'name' => 'Banner Giới Thiệu Công Nghệ',
                'slug' => 'banner-gioi-thieu-cong-nghe',
                'album' => [
                    '/images/slide/test11.jpg',
                ],
                'page_id' => $homePageId,
                'position_id' => $introPosId,
                'link_url' => '/shop',
                'order' => 1,
                'is_hot' => true,
                'publish' => 2,
            ],
            [
                'name' => 'Banner Phụ Kiện Cao Cấp',
                'slug' => 'banner-phu-kien-cao-cap',
                'album' => [
                    '/images/slide/image_about_accessory_1.jpg',
                ],
                'page_id' => $homePageId,
                'position_id' => $introPosId,
                'link_url' => '/shop',
                'order' => 2,
                'is_hot' => false,
                'publish' => 2,
            ],
            [
                'name' => 'Banner Đầu Trang Shop',
                'slug' => 'banner-dau-trang-shop',
                'album' => [
                    '/images/slide/banner-slide-shop-top_1.jpg',
                    '/images/slide/banner-slide-shop-top_2.jpg',
                ],
                'page_id' => $shopPageId,
                'position_id' => $mainPosId,
                'link_url' => '/shop',
                'order' => 1,
                'is_hot' => true,
                'publish' => 2,
            ],
            [
                'name' => 'Banner Mùa Vụ Khuyến Mãi Shop',
                'slug' => 'banner-mua-vu-khuyen-mai-shop',
                'album' => [
                    '/images/slide/buy_smart.jpg',
                ],
                'page_id' => $shopPageId,
                'position_id' => $seasonPosId,
                'link_url' => '/shop',
                'order' => 1,
                'is_hot' => true,
                'publish' => 2,
            ],
        ];

        foreach ($banners as $item) {
            Banner::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'album' => $item['album'],
                    'page_id' => $item['page_id'],
                    'position_id' => $item['position_id'],
                    'link_url' => $item['link_url'],
                    'order' => $item['order'],
                    'is_hot' => $item['is_hot'],
                    'publish' => $item['publish'],
                    'user_id' => 1,
                ]
            );
        }
    }
}
