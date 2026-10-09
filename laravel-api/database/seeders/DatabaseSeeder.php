<?php

namespace Database\Seeders;

use Database\Seeders\Post\PostCatalogueSeeder;
use Database\Seeders\Product\ProductCatalogueSeeder;
use Database\Seeders\Product\TagSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserCatalogueSeeder::class,
            UserSeeder::class,
            AdminPermissionSeeder::class,
            ProductCatalogueSeeder::class,
            TagSeeder::class,
            PostCatalogueSeeder::class,
            \Database\Seeders\Post\TagSeeder::class,
            \Database\Seeders\Attribute\AttributeCatalogueSeeder::class,
            \Database\Seeders\Attribute\AttributeSeeder::class,
            \Database\Seeders\Faq\FaqCatalogueSeeder::class,
            \Database\Seeders\Faq\FaqSeeder::class,
            \Database\Seeders\Brand\BrandSeeder::class,
            \Database\Seeders\Agency\AgencySeeder::class,
            \Database\Seeders\Agency\AgencyWorkingHourSeeder::class,
            \Database\Seeders\Product\ProductSeeder::class,
            \Database\Seeders\System\SystemSeeder::class,
            \Database\Seeders\Post\PostSeeder::class,
            \Database\Seeders\Menu\MenuSeeder::class,
            PromotionSeeder::class,
        ]);
    }
}
