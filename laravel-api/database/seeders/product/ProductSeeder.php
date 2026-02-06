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
        // 1. Prepare Data Helpers & Ensure Dependencies Exist

        // Attribute Catalogues
        $attrCatGeneral = AttributeCatalogue::firstOrCreate(
            ['slug' => 'thong-so-chung'],
            ['name' => 'Thông số chung', 'publish' => 1, 'user_id' => 1]
        );

        // Brands (Generic list + Specifics)
        $brandNames = ['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Asus', 'Dell', 'Lenovo', 'Sony', 'JBL', 'HP'];
        foreach ($brandNames as $name) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'publish' => 2, 'user_id' => 1] // Publish = 2
            );
        }

        // Product Catalogues
        $catPhone = ProductCatalogue::firstOrCreate(['slug' => 'dien-thoai'], ['name' => 'Điện thoại', 'publish' => 2, 'level' => 1, 'user_id' => 1]);
        $catLaptop = ProductCatalogue::firstOrCreate(['slug' => 'laptop'], ['name' => 'Laptop', 'publish' => 2, 'level' => 1, 'user_id' => 1]);
        $catTablet = ProductCatalogue::firstOrCreate(['slug' => 'tablet'], ['name' => 'Tablet', 'publish' => 2, 'level' => 1, 'user_id' => 1]);
        $catAudio = ProductCatalogue::firstOrCreate(['slug' => 'am-thanh'], ['name' => 'Âm thanh', 'publish' => 2, 'level' => 1, 'user_id' => 1]);

        // Tags (Ensure basic infrastructure exists)
        // We rely on TagSeeder mostly, but let's ensure specific ones exist for our products.
        $tagsMap = [
            'appleiphone' => 'Apple iPhone',
            'samsunggalaxy' => 'Samsung Galaxy',
            'laptopdell' => 'Laptop Dell',
            'laptopasus' => 'Laptop Asus',
            'tainghe' => 'Tai nghe',
            'loa' => 'Loa',
            'flagship' => 'Flagship',
            'giare' => 'Giá rẻ',
            'gaming' => 'Gaming'
        ];
        foreach ($tagsMap as $slug => $name) {
            ProductTag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'publish' => 2, 'is_hot' => 1, 'user_id' => 1]
            );
        }


        // 2. Define Product Data with Dynamic References
        $products = [
            // --- SMARTPHONES ---
            [
                'name' => 'iPhone 15 Pro Max',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 34990000,
                'is_hot' => true,
                'tag_slugs' => ['appleiphone', 'flagship'],
                'variants' => [
                    ['sku' => 'IP15PM-256-NAT', 'price' => 29490000, 'attrs' => ['256GB', 'Titan Tự Nhiên']],
                    ['sku' => 'IP15PM-512-BLU', 'price' => 35990000, 'attrs' => ['512GB', 'Xanh Titan']],
                ]
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'brand_name' => 'Samsung',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 33990000,
                'is_hot' => true,
                'tag_slugs' => ['samsunggalaxy', 'flagship'],
                'variants' => [
                    ['sku' => 'S24U-256-GRY', 'price' => 29990000, 'attrs' => ['256GB', 'Xám Titan']],
                    ['sku' => 'S24U-512-BLK', 'price' => 33990000, 'attrs' => ['512GB', 'Đen Titan']],
                ]
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'brand_name' => 'Xiaomi',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 32990000,
                'is_hot' => true,
                'tag_slugs' => ['flagship'],
                'variants' => [
                    ['sku' => 'MI14U-512-BLK', 'price' => 32990000, 'attrs' => ['512GB', 'Đen']],
                    ['sku' => 'MI14U-512-WHT', 'price' => 32990000, 'attrs' => ['512GB', 'Trắng']],
                ]
            ],
            [
                'name' => 'OPPO Find N3 Flip',
                'brand_name' => 'Oppo',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 22990000,
                'is_hot' => false,
                'tag_slugs' => ['flagship'],
                'variants' => [
                    ['sku' => 'N3FLIP-256-GLD', 'price' => 22990000, 'attrs' => ['256GB', 'Vàng']],
                ]
            ],
            [
                'name' => 'iPhone 13',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'dien-thoai',
                'price_origin' => 15990000,
                'is_hot' => false,
                'tag_slugs' => ['appleiphone'],
                'variants' => [
                    ['sku' => 'IP13-128-PNK', 'price' => 13990000, 'attrs' => ['128GB', 'Hồng']],
                ]
            ],

            // --- LAPTOPS ---
            [
                'name' => 'MacBook Air M3 13 inch',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'laptop',
                'price_origin' => 27990000,
                'is_hot' => true,
                'tag_slugs' => ['laptop'], // using generic tag if specific missing
                'variants' => [
                    ['sku' => 'MBA-M3-8-256-GRY', 'price' => 27490000, 'attrs' => ['8GB', '256GB', 'Xám không gian', 'Apple M3']],
                    ['sku' => 'MBA-M3-16-512-SIL', 'price' => 32990000, 'attrs' => ['16GB', '512GB', 'Bạc', 'Apple M3']],
                ]
            ],
            [
                'name' => 'MacBook Pro 14 inch M3 Pro',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'laptop',
                'price_origin' => 49990000,
                'is_hot' => true,
                'tag_slugs' => ['flagship'],
                'variants' => [
                    ['sku' => 'MBP-M3P-18-512-BLK', 'price' => 48990000, 'attrs' => ['18GB', '512GB', 'Đen không gian']],
                ]
            ],
            [
                'name' => 'Asus ROG Zephyrus G14',
                'brand_name' => 'Asus',
                'catalogue_slug' => 'laptop',
                'price_origin' => 45990000,
                'is_hot' => true,
                'tag_slugs' => ['laptopasus', 'gaming'],
                'variants' => [
                    ['sku' => 'ROG-G14-32-1TB', 'price' => 45990000, 'attrs' => ['32GB', '1TB', 'Trắng']],
                ]
            ],
            [
                'name' => 'Dell XPS 13 Plus',
                'brand_name' => 'Dell',
                'catalogue_slug' => 'laptop',
                'price_origin' => 55000000,
                'is_hot' => false,
                'tag_slugs' => ['laptopdell'],
                'variants' => [
                    ['sku' => 'XPS13-16-512', 'price' => 54990000, 'attrs' => ['16GB', '512GB', 'Bạc']],
                ]
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'brand_name' => 'Lenovo',
                'catalogue_slug' => 'laptop',
                'price_origin' => 42000000,
                'is_hot' => false,
                'tag_slugs' => ['flagship'],
                'variants' => [
                    ['sku' => 'X1-16-512', 'price' => 41500000, 'attrs' => ['16GB', '512GB', 'Đen']],
                ]
            ],

            // --- TABLETS ---
            [
                'name' => 'iPad Pro M4 11 inch',
                'brand_name' => 'Apple',
                'catalogue_slug' => 'tablet',
                'price_origin' => 28990000,
                'is_hot' => true,
                'tag_slugs' => ['flagship', 'tabletipad'],
                'variants' => [
                    ['sku' => 'IPAD-M4-256-GRY', 'price' => 28990000, 'attrs' => ['256GB', 'Đen không gian']],
                ]
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra',
                'brand_name' => 'Samsung',
                'catalogue_slug' => 'tablet',
                'price_origin' => 25990000,
                'is_hot' => false,
                'tag_slugs' => ['samsungtablet'],
                'variants' => [
                    ['sku' => 'TABS9U-512-GRY', 'price' => 24990000, 'attrs' => ['512GB', 'Xám']],
                ]
            ],
            [
                'name' => 'Xiaomi Pad 6',
                'brand_name' => 'Xiaomi',
                'catalogue_slug' => 'tablet',
                'price_origin' => 8990000,
                'is_hot' => true,
                'tag_slugs' => ['giare'],
                'variants' => [
                    ['sku' => 'MIPAD6-8-256', 'price' => 8490000, 'attrs' => ['8GB', '256GB', 'Xám']],
                ]
            ],

            // --- AUDIO ---
            [
                'name' => 'Sony WF-1000XM5',
                'brand_name' => 'Sony',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 6490000,
                'is_hot' => false,
                'tag_slugs' => ['tainghe'],
                'variants' => [
                    ['sku' => 'XM5-BLK', 'price' => 5990000, 'attrs' => ['Đen']],
                    ['sku' => 'XM5-SIL', 'price' => 5990000, 'attrs' => ['Bạc']],
                ]
            ],
            [
                'name' => 'JBL PartyBox 310',
                'brand_name' => 'JBL',
                'catalogue_slug' => 'am-thanh',
                'price_origin' => 11900000,
                'is_hot' => true,
                'tag_slugs' => ['loa'],
                'variants' => [
                    ['sku' => 'JBL-PB310', 'price' => 10900000, 'attrs' => ['Đen']],
                ]
            ],
        ];

        foreach ($products as $idx => $prodData) {
            DB::transaction(function () use ($prodData, $idx, $attrCatGeneral) {
                // Dynamic Lookups
                $brand = Brand::where('name', $prodData['brand_name'])->first();
                $catalogue = ProductCatalogue::where('slug', $prodData['catalogue_slug'])->first();

                // Tags
                $tagIds = [];
                $mainTagId = 0;
                if (!empty($prodData['tag_slugs'])) {
                    $tags = ProductTag::whereIn('slug', $prodData['tag_slugs'])->get();
                    $tagIds = $tags->pluck('id')->toArray();
                    $mainTagId = $tagIds[0] ?? 0;
                }

                // 2. Create/Update Product
                $product = Product::firstOrCreate(
                    ['slug' => Str::slug($prodData['name'])],
                    [
                        'name' => $prodData['name'],
                        'sku' => $prodData['variants'][0]['sku'] . '-MASTER',
                        'description' => $prodData['name'] . ' - Hàng chính hãng, giá tốt nhất tại TechMart.',
                        'content' => '<p>Chi tiết sản phẩm ' . $prodData['name'] . '...</p>',
                        'price_origin' => $prodData['price_origin'],
                        'sort_order' => $idx + 1,
                        'product_catalogue_id' => $catalogue ? $catalogue->id : 1, // Fallback safe
                        'brand_id' => $brand ? $brand->id : null,
                        'tag_id' => $mainTagId, // Main Tag ID
                        'is_hot' => $prodData['is_hot'],
                        'user_id' => 1,
                        'publish' => 2,
                    ]
                );

                // Sync Relations
                $product->product_catalogues()->syncWithoutDetaching([$catalogue ? $catalogue->id : 1]);
                if (!empty($tagIds)) {
                    $product->product_tags()->syncWithoutDetaching($tagIds);
                }

                // 3. Create Variants for Product
                foreach ($prodData['variants'] as $variantData) {
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $variantData['sku']],
                        [
                            'name' => $prodData['name'] . ' ' . implode(' ', $variantData['attrs']),
                            'code' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'stock' => rand(10, 100),
                            'product_id' => $product->id,
                            'user_id' => 1,
                            'publish' => 2,
                        ]
                    );

                    // 4. Attach Attributes (Dynamic)
                    $attrIds = [];
                    $attrData = [];

                    foreach ($variantData['attrs'] as $attrName) {
                        // Dynamic lookup by name or slug
                        $attr = Attribute::where('name', $attrName)->orWhere('slug', Str::slug($attrName))->first();

                        if (!$attr) {
                            // FirstOrCreate safely
                            $attr = Attribute::firstOrCreate(
                                ['slug' => Str::slug($attrName)],
                                [
                                    'name' => $attrName,
                                    'attribute_catalogue_id' => $attrCatGeneral->id,
                                    'publish' => 1,
                                    'user_id' => 1
                                ]
                            );
                        }
                        $id = $attr->id;

                        $attrIds[] = $id;
                        $attrData[$id] = $attrName;
                    }

                    // Attach pivot
                    $variant->variant_attributes()->syncWithoutDetaching($attrIds);

                    // Update JSON data
                    $variant->update(['attribute_data' => $attrData]);
                }
            });
        }
    }
}
