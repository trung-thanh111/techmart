<?php

namespace Database\Seeders\Brand;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'website' => 'https://www.apple.com',
                'country' => 'USA',
                'description' => 'Apple Inc. is an American multinational technology company that specializes in consumer electronics, software and online services.',
            ],
            [
                'name' => 'Samsung',
                'website' => 'https://www.samsung.com',
                'country' => 'South Korea',
                'description' => 'Samsung Electronics Co., Ltd. is a South Korean multinational electronics corporation.',
            ],
            [
                'name' => 'Sony',
                'website' => 'https://www.sony.com',
                'country' => 'Japan',
                'description' => 'Sony Group Corporation is a Japanese multinational conglomerate corporation.',
            ],
            [
                'name' => 'Dell',
                'website' => 'https://www.dell.com',
                'country' => 'USA',
                'description' => 'Dell is an American multinational computer technology company.',
            ],
            [
                'name' => 'HP',
                'website' => 'https://www.hp.com',
                'country' => 'USA',
                'description' => 'Hewlett-Packard was an American multinational information technology company.',
            ],
            [
                'name' => 'Asus',
                'website' => 'https://www.asus.com',
                'country' => 'Taiwan',
                'description' => 'ASUSTeK Computer Inc. is a Taiwanese multinational computer and phone hardware and electronics company.',
            ],
            [
                'name' => 'Acer',
                'website' => 'https://www.acer.com',
                'country' => 'Taiwan',
                'description' => 'Acer Inc. is a Taiwanese multinational hardware and electronics corporation.',
            ],
            [
                'name' => 'Lenovo',
                'website' => 'https://www.lenovo.com',
                'country' => 'China',
                'description' => 'Lenovo Group Limited is a Chinese multinational technology company.',
            ],
            [
                'name' => 'Xiaomi',
                'website' => 'https://www.mi.com',
                'country' => 'China',
                'description' => 'Xiaomi Corporation is a Chinese designer and manufacturer of consumer electronics.',
            ],
            [
                'name' => 'Oppo',
                'website' => 'https://www.oppo.com',
                'country' => 'China',
                'description' => 'Oppo Electronics Corp is a Chinese consumer electronics and mobile communications company.',
            ],
            [
                'name' => 'Logitech',
                'website' => 'https://www.logitech.com',
                'country' => 'Switzerland',
                'description' => 'Logitech International S.A. is a Swiss-American manufacturer of computer peripherals and software.',
            ],
            [
                'name' => 'JBL',
                'website' => 'https://www.jbl.com',
                'country' => 'USA',
                'description' => 'JBL is an American audio equipment manufacturer.',
            ],
        ];

        foreach ($brands as $key => $brand) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'description' => $brand['description'],
                    'website' => $brand['website'],
                    'country' => $brand['country'],
                    'order' => $key + 1,
                    'publish' => 2,
                    'user_id' => 1,
                ]
            );
        }
    }
}
