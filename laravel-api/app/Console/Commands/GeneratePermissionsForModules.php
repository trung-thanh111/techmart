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
        'product_reviews',
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
        'orders',
        'faqs',
        'faq_catalogues',
        'agencies',
        'agency_working_hours',
        'inventories',
        'menus',
        'menu_items',
        'dashboard',
        'address_orders',
        'images',
        'mails',
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

        // Gán toàn bộ permissions cho nhóm Super Admin
        $adminCatalogue = \App\Models\UserCatalogue::where('is_admin', true)
            ->orWhere('slug', 'admin')
            ->orWhere('id', 2)
            ->first();

        if ($adminCatalogue) {
            $allPermissionIds = \App\Models\Permission::pluck('id')->toArray();
            $adminCatalogue->permissions()->sync($allPermissionIds);
            $this->info("✓ Assigned all " . count($allPermissionIds) . " permissions to Super Admin catalogue: {$adminCatalogue->name}");

            // Đảm bảo các admin users được liên kết vào catalogue
            $adminUsers = \App\Models\User::where('user_catalogue_id', $adminCatalogue->id)->get();
            foreach ($adminUsers as $adminUser) {
                if (!$adminUser->user_catalogues()->where('user_catalogues.id', $adminCatalogue->id)->exists()) {
                    $adminUser->user_catalogues()->attach($adminCatalogue->id);
                }
            }
            $this->info("✓ Linked " . $adminUsers->count() . " admin user(s) to catalogue: {$adminCatalogue->name}");
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
