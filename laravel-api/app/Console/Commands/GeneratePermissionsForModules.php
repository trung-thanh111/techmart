<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Observers\PermissionObserver;

class GeneratePermissionsForModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:permissions-for-modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions for all existing admin modules';

    /**
     * Danh sách các module đã triển khai
     */
    protected array $modules = [
        'users',
        'user_catalogues',
        'permissions',
        'products',
        'product_catalogues',
        'product_variants',
        'product_tags',
        'brands',
        'attributes',
        'attribute_catalogues',
        'posts',
        'post_catalogues',
        'post_tags',
        'post_comments',
        'post_likes',
        'post_comment_likes',
        'banners',
        'banner_pages',
        'banner_positions',
        'promotions',
        'promotion_catalogues',
        'faqs',
        'faq_catalogues',
        'agencies',
        'agency_working_hours',
        'inventories',
        'address_orders',
        'images',
        'systems',
        'popups',
        'carts',
        'cart_items',
        'wishlists',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate permissions for all modules...');
        $this->newLine();

        $permissionObserver = app(PermissionObserver::class);
        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($this->modules as $module) {
            try {
                $created = $permissionObserver->generateForModule($module);
                $count = count($created);

                if ($count > 0) {
                    $this->info("✓ Generated {$count} permissions for module: {$module}");
                    $totalCreated += $count;
                } else {
                    $this->warn("⊘ Permissions for module {$module} already exist, skipping...");
                    $totalSkipped++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to generate permissions for module {$module}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  - Total permissions created: {$totalCreated}");
        $this->info("  - Modules skipped (already exist): {$totalSkipped}");
        $this->info("  - Total modules processed: " . count($this->modules));
        $this->newLine();
        $this->info('Permission generation completed!');
    }
}
