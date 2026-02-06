<?php

namespace Database\Seeders\Agency;

use App\Models\Agency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            [
                'name' => 'TechMart Hà Nội',
                'description' => 'Trụ sở chính của TechMart tại Hà Nội, cung cấp đầy đủ các sản phẩm công nghệ và trung tâm bảo hành.',
                'website' => 'https://techmart.vn',
                'email' => 'hanoi@techmart.vn',
                'phone' => '024.1234.5678',
                'address' => '123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
                'iframe_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.899723385731!2d105.79547531476682!3d21.03669738599411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab3a94dfdb53%3A0x6b7774d816a3a411!2sCau%20Giay%20St!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'is_main' => 1,
            ],
            [
                'name' => 'TechMart TP. Hồ Chí Minh (Trụ sở chính)',
                'description' => 'Chi nhánh lớn nhất tại khu vực phía Nam, trung tâm trải nghiệm sản phẩm cao cấp.',
                'website' => 'https://techmart.vn',
                'email' => 'hcm@techmart.vn',
                'phone' => '028.9876.5432',
                'address' => '456 Đường Nguyễn Thị Minh Khai, Quận 3, TP. Hồ Chí Minh',
                'iframe_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.424368565565!2d106.6880923148008!3d10.77877299231362!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f28f4044715%3A0x444c4fa61633519c!2sNguyen%20Thi%20Minh%20Khai!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'is_main' => 0,
            ],
            [
                'name' => 'TechMart Đà Nẵng',
                'description' => 'Chi nhánh phục vụ khu vực miền Trung với đầy đủ dịch vụ bán hàng và hậu mãi.',
                'website' => 'https://techmart.vn',
                'email' => 'danang@techmart.vn',
                'phone' => '0236.3333.999',
                'address' => '789 Đường Nguyễn Văn Linh, Quận Hải Châu, Đà Nẵng',
                'iframe_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3834.1104354063!2d108.217316314856!3d16.059758988887!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314219b5dce5e0cb%3A0x7d0a6c6d05051b43!2sNguyen%20Van%20Linh!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'is_main' => 0,
            ],
            [
                'name' => 'TechMart Hải Phòng',
                'description' => 'Chi nhánh chính hãng tại thành phố cảng Hải Phòng.',
                'website' => 'https://techmart.vn',
                'email' => 'haiphong@techmart.vn',
                'phone' => '0225.1111.222',
                'address' => '321 Đường Lạch Tray, Quận Ngô Quyền, Hải Phòng',
                'iframe_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3728.8091768465!2d106.694362314763!3d20.852932986093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a7af26887e7df%3A0x6b2b7371c667087!2sLach%20Tray!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'is_main' => 0,
            ],
            [
                'name' => 'TechMart Cần Thơ',
                'description' => 'Trung tâm công nghệ lớn nhất khu vực Đồng bằng sông Cửu Long.',
                'website' => 'https://techmart.vn',
                'email' => 'cantho@techmart.vn',
                'phone' => '0292.8888.666',
                'address' => '101 Đường 30/4, Quận Ninh Kiều, Cần Thơ',
                'iframe_map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.8415177286!2d105.768427314795!3d10.029933992830!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a0895a51d60719%3A0x9d76b0035f6d53d0!2s30%20Thang%204!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'is_main' => 0,
            ],
        ];

        foreach ($agencies as $agency) {
            Agency::create([
                'name' => $agency['name'],
                'slug' => Str::slug($agency['name']),
                'description' => $agency['description'],
                'logo' => null,
                'website' => $agency['website'],
                'email' => $agency['email'],
                'phone' => $agency['phone'],
                'address' => $agency['address'],
                'iframe_map' => $agency['iframe_map'],
                'is_main' => $agency['is_main'],
                'is_visible' => 1,
                'publish' => 2,
                'user_id' => 1,
            ]);
        }
    }
}
