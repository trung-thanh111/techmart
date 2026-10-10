<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình các trường hệ thống động (Dynamic System Settings)
    |--------------------------------------------------------------------------
    | Thêm mới trường cấu hình tại đây mà KHÔNG CẦN migration database.
    | Mỗi trường sẽ tự động sinh keyword dạng: {group}_{field_key}
    */
    'groups' => [
        'general' => [
            'label' => 'Thông tin chung',
            'description' => 'Tên công ty, thương hiệu, khẩu hiệu, logo, favicon và bản quyền.',
            'fields' => [
                'company_name' => [
                    'label' => 'Tên công ty',
                    'type' => 'text',
                    'placeholder' => 'Công ty Cổ phần Công nghệ TechMart',
                    'default' => 'Công ty Cổ phần Công nghệ TechMart',
                ],
                'short_name' => [
                    'label' => 'Tên thương hiệu',
                    'type' => 'text',
                    'placeholder' => 'TechMart',
                    'default' => 'TechMart',
                ],
                'slogan' => [
                    'label' => 'Khẩu hiệu',
                    'type' => 'text',
                    'placeholder' => 'Công nghệ đỉnh cao - Giá trị bền vững',
                    'default' => 'Công nghệ đỉnh cao - Giá trị bền vững',
                ],
                'logo' => [
                    'label' => 'Logo',
                    'type' => 'image',
                    'placeholder' => '/images/logo/logo-website.png',
                    'default' => '/images/logo/logo-website.png',
                ],
                'favicon' => [
                    'label' => 'Favicon',
                    'type' => 'image',
                    'placeholder' => '/images/favicon.ico',
                    'default' => '/images/favicon.ico',
                ],
                'copyright' => [
                    'label' => 'Bản quyền',
                    'type' => 'text',
                    'placeholder' => '© 2026 TechMart. Bản quyền thuộc về Công ty Cổ phần Công nghệ TechMart.',
                    'default' => '© 2026 TechMart. Bản quyền thuộc về Công ty Cổ phần Công nghệ TechMart.',
                ],
                'tax_code' => [
                    'label' => 'Mã số thuế',
                    'type' => 'text',
                    'placeholder' => '0109876543 do Sở KH&ĐT TP.HCM cấp',
                    'default' => '0109876543 do Sở KH&ĐT TP.HCM cấp',
                ],
                'working_hours' => [
                    'label' => 'Thời gian làm việc',
                    'type' => 'text',
                    'placeholder' => '8:00 - 22:00 (Tất cả các ngày trong tuần)',
                    'default' => '8:00 - 22:00 (Tất cả các ngày trong tuần)',
                ],
                'intro' => [
                    'label' => 'Giới thiệu',
                    'type' => 'textarea',
                    'placeholder' => 'Hệ thống bán lẻ thiết bị công nghệ chính hãng hàng đầu Việt Nam.',
                    'default' => 'Hệ thống bán lẻ thiết bị công nghệ chính hãng hàng đầu Việt Nam.',
                ],
            ],
        ],

        'contact' => [
            'label' => 'Thông tin liên hệ',
            'description' => 'Hotline, email, địa chỉ trụ sở và bản đồ.',
            'fields' => [
                'hotline' => [
                    'label' => 'Hotline tư vấn',
                    'type' => 'text',
                    'placeholder' => '1900 2098',
                    'default' => '1900 2098',
                ],
                'hotline_secondary' => [
                    'label' => 'Hotline bán hàng',
                    'type' => 'text',
                    'placeholder' => '1800 6975',
                    'default' => '1800 6975',
                ],
                'complaints_hotline' => [
                    'label' => 'Hotline hỗ trợ',
                    'type' => 'text',
                    'placeholder' => '1800 2097',
                    'default' => '1800 2097',
                ],
                'email' => [
                    'label' => 'Email hỗ trợ',
                    'type' => 'email',
                    'placeholder' => 'cskh@techmart.vn',
                    'default' => 'cskh@techmart.vn',
                ],
                'email_complaint' => [
                    'label' => 'Email',
                    'type' => 'email',
                    'placeholder' => 'hotro@techmart.vn',
                    'default' => 'hotro@techmart.vn',
                ],
                'address' => [
                    'label' => 'Địa chỉ trụ sở chính',
                    'type' => 'text',
                    'placeholder' => '128 Trần Quang Khải, Phường Tân Định, Quận 1, TP. Hồ Chí Minh',
                    'default' => '128 Trần Quang Khải, Phường Tân Định, Quận 1, TP. Hồ Chí Minh',
                ],
                'map_iframe' => [
                    'label' => 'Iframe map',
                    'type' => 'textarea',
                    'placeholder' => 'https://www.google.com/maps/embed?...',
                    'default' => '',
                ],
            ],
        ],

        'seo' => [
            'label' => 'Cấu hình SEO',
            'description' => 'Thiết lập meta title, description, keywords, OG image và robots.',
            'fields' => [
                'meta_title' => [
                    'label' => 'Tiêu đề',
                    'type' => 'text',
                    'placeholder' => 'TechMart - Hệ Thống Bán Lẻ Điện Thoại, Laptop, Phụ Kiện Chính Hãng',
                    'default' => 'TechMart - Hệ Thống Bán Lẻ Điện Thoại, Laptop, Phụ Kiện Chính Hãng',
                ],
                'meta_keywords' => [
                    'label' => 'Từ khóa',
                    'type' => 'text',
                    'placeholder' => 'techmart, điện thoại chính hãng, laptop giá rẻ, phụ kiện công nghệ, apple, samsung',
                    'default' => 'techmart, điện thoại chính hãng, laptop giá rẻ, phụ kiện công nghệ, apple, samsung',
                ],
                'meta_description' => [
                    'label' => 'Mô tả',
                    'type' => 'textarea',
                    'placeholder' => 'TechMart - Hệ thống bán lẻ thiết bị công nghệ hàng đầu Việt Nam. Cam kết 100% hàng chính hãng, giá cạnh tranh, bảo hành chu đáo, giao hàng siêu tốc.',
                    'default' => 'TechMart - Hệ thống bán lẻ thiết bị công nghệ hàng đầu Việt Nam. Cam kết 100% hàng chính hãng, giá cạnh tranh, bảo hành chu đáo, giao hàng siêu tốc.',
                ],
                'og_image' => [
                    'label' => 'Ảnh đại diện',
                    'type' => 'image',
                    'placeholder' => '/images/logo/logo-website.png',
                    'default' => '/images/logo/logo-website.png',
                ],
                'robots' => [
                    'label' => 'Robots Index',
                    'type' => 'text',
                    'placeholder' => 'index, follow',
                    'default' => 'index, follow',
                ],
            ],
        ],

        'social' => [
            'label' => 'Mạng xã hội',
            'description' => 'Liên kết đến các kênh truyền thông.',
            'fields' => [
                'facebook' => [
                    'label' => 'Facebook',
                    'type' => 'url',
                    'placeholder' => 'https://facebook.com/techmart.official',
                    'default' => 'https://facebook.com/techmart.official',
                ],
                'youtube' => [
                    'label' => 'YouTube',
                    'type' => 'url',
                    'placeholder' => 'https://youtube.com/@techmart.official',
                    'default' => 'https://youtube.com/@techmart.official',
                ],
                'tiktok' => [
                    'label' => 'TikTok',
                    'type' => 'url',
                    'placeholder' => 'https://tiktok.com/@techmart.official',
                    'default' => 'https://tiktok.com/@techmart.official',
                ],
                'zalo' => [
                    'label' => 'Zalo',
                    'type' => 'url',
                    'placeholder' => 'https://zalo.me/techmart',
                    'default' => 'https://zalo.me/techmart',
                ],
                'messenger' => [
                    'label' => 'Facebook',
                    'type' => 'url',
                    'placeholder' => 'https://m.me/techmart.official',
                    'default' => 'https://m.me/techmart.official',
                ],
                'instagram' => [
                    'label' => 'Instagram',
                    'type' => 'url',
                    'placeholder' => 'https://instagram.com/techmart.official',
                    'default' => 'https://instagram.com/techmart.official',
                ],
            ],
        ],

        'script' => [
            'label' => 'Nhúng Script',
            'description' => 'Mã theo dõi Google Tag Manager, Google Analytics.',
            'fields' => [
                'head_script' => [
                    'label' => 'Thẻ Script',
                    'type' => 'textarea',
                    'placeholder' => 'Google Tag Manager',
                    'default' => '',
                ],
                'body_script' => [
                    'label' => 'Thẻ Body',
                    'type' => 'textarea',
                    'placeholder' => 'Livechat script',
                    'default' => '',
                ],
            ],
        ],
    ],
];
