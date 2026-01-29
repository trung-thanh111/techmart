<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Menu\MenuItemController;
use App\Http\Controllers\Api\V1\Menu\MenuController;
use App\Http\Controllers\Api\V1\System\PopupController;
use App\Http\Controllers\Api\V1\System\SystemController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Banner\BannerPositionController;
use App\Http\Controllers\Api\V1\Banner\BannerPageController;
use App\Http\Controllers\Api\V1\Promotion\PromotionController;
use App\Http\Controllers\Api\V1\Product\ProductTagController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\User\UserCatalogueController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Product\ProductCatalogueController;
use App\Http\Controllers\Api\V1\Product\ProductVariantController;
use App\Http\Controllers\Api\V1\Brand\BrandController;
use App\Http\Controllers\Api\V1\Attribute\AttributeController;
use App\Http\Controllers\Api\V1\Attribute\AttributeCatalogueController;
use App\Http\Controllers\Api\V1\Post\PostController;
use App\Http\Controllers\Api\V1\Post\PostCatalogueController;
use App\Http\Controllers\Api\V1\Post\TagController as PostTagController;
use App\Http\Controllers\Api\V1\Faq\FaqController;
use App\Http\Controllers\Api\V1\Faq\FaqCatalogueController;
use App\Http\Controllers\Api\V1\Promotion\PromotionCatalogueController;
use App\Http\Controllers\Api\V1\AgencySystem\AgencyController;
use App\Http\Controllers\Api\V1\AgencySystem\AgencyWorkingHourController;
use App\Http\Controllers\Api\V1\Image\ImageController;
use App\Http\Controllers\Api\V1\Permission\PermissionController;
use App\Http\Controllers\Api\V1\Mail\MailController;
use App\Http\Controllers\Api\V1\Banner\BannerController;
use App\Http\Controllers\Api\V1\Post\PostCommentController;
use App\Http\Controllers\Api\V1\Post\PostCommentLikeController;
use App\Http\Controllers\Api\V1\Post\PostLikeController;

$routeExcept = ['create', 'edit'];

// Admin Auth routes
Route::group(['prefix' => 'v1/admin/auth'], function () {
    Route::post('refresh', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'refresh']);
    Route::middleware(['convert'])->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'login']);
    });
    Route::middleware(['jwt:admin', 'convert'])->group(function () {
        Route::get('me', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'me']);
        Route::post('logout', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'logout']);
    });
});

// Admin Resource Routes (Protected)
Route::group(['prefix' => 'v1/admin', 'middleware' => ['jwt:admin', 'convert', 'checkPermission']], function () use ($routeExcept) {

    // Attribute Catalogue
    Route::group(['prefix' => 'attribute_catalogues'], function () {
        Route::delete('bulkDelete', [AttributeCatalogueController::class, 'bulkDelete']);
        Route::post('{id}/attach', [AttributeCatalogueController::class, 'attach']);
        Route::delete('{id}/detach', [AttributeCatalogueController::class, 'detach']);
        Route::post('bulkStore', [AttributeCatalogueController::class, 'bulkStore']);
        Route::put('bulkUpdate', [AttributeCatalogueController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [AttributeCatalogueController::class, 'bulkUpdate']);
    });
    Route::resource('attribute_catalogues', AttributeCatalogueController::class)->except($routeExcept);

    // Attributes
    Route::group(['prefix' => 'attributes'], function () {
        Route::delete('bulkDelete', [AttributeController::class, 'bulkDelete']);
        Route::post('{id}/attach', [AttributeController::class, 'attach']);
        Route::delete('{id}/detach', [AttributeController::class, 'detach']);
        Route::post('bulkStore', [AttributeController::class, 'bulkStore']);
        Route::put('bulkUpdate', [AttributeController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [AttributeController::class, 'bulkUpdate']);
    });
    Route::resource('attributes', AttributeController::class)->except($routeExcept);

    // Permissions
    Route::group(['prefix' => 'permissions'], function () {
        Route::delete('bulkDelete', [PermissionController::class, 'bulkDelete']);
        Route::post('{id}/attach', [PermissionController::class, 'attach']);
        Route::delete('{id}/detach', [PermissionController::class, 'detach']);
        Route::post('bulkStore', [PermissionController::class, 'bulkStore']);
        Route::put('bulkUpdate', [PermissionController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [PermissionController::class, 'bulkUpdate']);
    });
    Route::resource('permissions', PermissionController::class)->except($routeExcept);

    // Post Catalogue (Admin Write)
    Route::group(['prefix' => 'post_catalogues'], function () {
        Route::delete('bulkDelete', [PostCatalogueController::class, 'bulkDelete']);
        Route::post('{id}/attach', [PostCatalogueController::class, 'attach']);
        Route::delete('{id}/detach', [PostCatalogueController::class, 'detach']);
        Route::post('bulkStore', [PostCatalogueController::class, 'bulkStore']);
        Route::put('bulkUpdate', [PostCatalogueController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [PostCatalogueController::class, 'bulkUpdate']);
    });
    Route::resource('post_catalogues', PostCatalogueController::class)->except($routeExcept);

    // Posts (Admin Write)
    Route::group(['prefix' => 'posts'], function () {
        Route::delete('bulkDelete', [PostController::class, 'bulkDelete']);
        Route::post('{id}/attach', [PostController::class, 'attach']);
        Route::delete('{id}/detach', [PostController::class, 'detach']);
        Route::post('bulkStore', [PostController::class, 'bulkStore']);
        Route::put('bulkUpdate', [PostController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [PostController::class, 'bulkUpdate']);
    });
    Route::resource('posts', PostController::class)->except($routeExcept);

    // Post Tags
    Route::group(['prefix' => 'post_tags'], function () {
        Route::delete('bulkDelete', [PostTagController::class, 'bulkDelete']);
        Route::post('{id}/attach', [PostTagController::class, 'attach']);
        Route::delete('{id}/detach', [PostTagController::class, 'detach']);
        Route::post('bulkStore', [PostTagController::class, 'bulkStore']);
        Route::put('bulkUpdate', [PostTagController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [PostTagController::class, 'bulkUpdate']);
    });
    Route::resource('post_tags', PostTagController::class)->except($routeExcept);

    // Product Catalogue (Admin Write)
    Route::group(['prefix' => 'product_catalogues'], function () {
        Route::delete('bulkDelete', [ProductCatalogueController::class, 'bulkDelete']);
        Route::post('{id}/attach', [ProductCatalogueController::class, 'attach']);
        Route::delete('{id}/detach', [ProductCatalogueController::class, 'detach']);
        Route::post('bulkStore', [ProductCatalogueController::class, 'bulkStore']);
        Route::put('bulkUpdate', [ProductCatalogueController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [ProductCatalogueController::class, 'bulkUpdate']);
    });
    Route::resource('product_catalogues', ProductCatalogueController::class)->except($routeExcept);

    // Products (Admin Write)
    Route::group(['prefix' => 'products'], function () {
        Route::delete('bulkDelete', [ProductController::class, 'bulkDelete']);
        Route::post('{id}/attach', [ProductController::class, 'attach']);
        Route::delete('{id}/detach', [ProductController::class, 'detach']);
        Route::post('bulkStore', [ProductController::class, 'bulkStore']);
        Route::put('bulkUpdate', [ProductController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [ProductController::class, 'bulkUpdate']);
    });
    Route::resource('products', ProductController::class)->except($routeExcept);

    // Brands
    Route::group(['prefix' => 'brands'], function () {
        Route::delete('bulkDelete', [BrandController::class, 'bulkDelete']);
        Route::post('{id}/attach', [BrandController::class, 'attach']);
        Route::delete('{id}/detach', [BrandController::class, 'detach']);
        Route::post('bulkStore', [BrandController::class, 'bulkStore']);
        Route::put('bulkUpdate', [BrandController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [BrandController::class, 'bulkUpdate']);
    });
    Route::resource('brands', BrandController::class)->except($routeExcept);

    // Product Variants
    Route::group(['prefix' => 'product_variants'], function () {
        Route::delete('bulkDelete', [ProductVariantController::class, 'bulkDelete']);
        Route::post('{id}/attach', [ProductVariantController::class, 'attach']);
        Route::delete('{id}/detach', [ProductVariantController::class, 'detach']);
        Route::post('bulkStore', [ProductVariantController::class, 'bulkStore']);
        Route::put('bulkUpdate', [ProductVariantController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [ProductVariantController::class, 'bulkUpdate']);
    });
    Route::resource('product_variants', ProductVariantController::class)->except($routeExcept);

    // Users & Catalogues
    Route::group(['prefix' => 'user_catalogues'], function () {
        Route::delete('bulkDelete', [UserCatalogueController::class, 'bulkDelete']);
        Route::post('{id}/attach', [UserCatalogueController::class, 'attach']);
        Route::delete('{id}/detach', [UserCatalogueController::class, 'detach']);
        Route::post('bulkStore', [UserCatalogueController::class, 'bulkStore']);
        Route::put('bulkUpdate', [UserCatalogueController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [UserCatalogueController::class, 'bulkUpdate']);
        Route::get('{id}/permissions', [UserCatalogueController::class, 'getPermissions']);
    });
    Route::resource('user_catalogues', UserCatalogueController::class)->except($routeExcept);

    Route::group(['prefix' => 'users'], function () {
        Route::delete('bulkDelete', [UserController::class, 'bulkDelete']);
        Route::post('{id}/attach', [UserController::class, 'attach']);
        Route::delete('{id}/detach', [UserController::class, 'detach']);
        Route::post('bulkStore', [UserController::class, 'bulkStore']);
        Route::put('bulkUpdate', [UserController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [UserController::class, 'bulkUpdate']);
    });
    Route::resource('users', UserController::class)->except($routeExcept);

    // Banners (Admin Write)
    Route::group(['prefix' => 'banners'], function () {
        Route::delete('bulkDelete', [BannerController::class, 'bulkDelete']);
        Route::post('{id}/attach', [BannerController::class, 'attach']);
        Route::delete('{id}/detach', [BannerController::class, 'detach']);
        Route::post('bulkStore', [BannerController::class, 'bulkStore']);
        Route::put('bulkUpdate', [BannerController::class, 'bulkUpdate']);
        Route::patch('bulkUpdate', [BannerController::class, 'bulkUpdate']);
    });
    Route::resource('banners', BannerController::class)->except($routeExcept);

    // Other Admin Resources
    Route::resource('systems', SystemController::class)->except($routeExcept);
    Route::resource('popups', PopupController::class)->except($routeExcept);
    Route::resource('promotions', PromotionController::class)->except($routeExcept);
    Route::resource('menus', MenuController::class)->except($routeExcept);
    Route::resource('menu_items', MenuItemController::class)->except($routeExcept);

    // Admin specific Logic for MenuItems
    Route::post('menu_items/rebuild-tree', [MenuItemController::class, 'rebuildTree']);
    Route::post('menu_items/drag-drop', [MenuItemController::class, 'dragDrop']);

    Route::resource('inventorys', InventoryController::class)->except($routeExcept);
    Route::resource('agencies', AgencyController::class)->except($routeExcept);
    Route::resource('agency_working_hours', AgencyWorkingHourController::class)->except($routeExcept);
    Route::resource('images', ImageController::class)->except($routeExcept);
    Route::resource('mails', MailController::class)->except($routeExcept);
    Route::resource('faq_catalogues', FaqCatalogueController::class)->except($routeExcept);
    Route::resource('faqs', FaqController::class)->except($routeExcept);
    Route::resource('banner_pages', BannerPageController::class)->except($routeExcept);
    Route::resource('banner_positions', BannerPositionController::class)->except($routeExcept);
    Route::resource('promotion_catalogues', PromotionCatalogueController::class)->except($routeExcept);
    Route::resource('product_tags', ProductTagController::class)->except($routeExcept);

    // Post Comments & Likes (Admin Moderation)
    Route::resource('post_comments', PostCommentController::class)->except($routeExcept);
    Route::resource('post_likes', PostLikeController::class)->except($routeExcept);
    Route::resource('post_comment_likes', PostCommentLikeController::class)->except($routeExcept);
});
