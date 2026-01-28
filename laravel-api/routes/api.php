<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Menu\MenuItemController;
use App\Http\Controllers\Api\V1\Menu\MenuController;
use App\Http\Controllers\Api\V1\System\PopupController;
use App\Http\Controllers\Api\V1\System\SystemController;
use App\Http\Controllers\Api\V1\Cart\CartItemController;
use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\Product\WishlistController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Banner\BannerPositionController;
use App\Http\Controllers\Api\V1\Banner\BannerPageController;
use App\Http\Controllers\Api\V1\Promotion\PromotionController;
use App\Http\Controllers\Api\V1\Post\PostCommentLikeController;
use App\Http\Controllers\Api\V1\Post\PostLikeController;
use App\Http\Controllers\Api\V1\Post\PostCommentController;
use App\Http\Controllers\Api\V1\Product\ProductTagController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
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
use App\Http\Controllers\Api\V1\User\AddressOrderController;
use App\Http\Controllers\Api\V1\Banner\BannerController;
use App\Http\Controllers\Api\V1\EmailVerification\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\ChangePasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

$routeExcept = ['create', 'edit'];

// Auth routes
Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::middleware(['convert'])->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });
    Route::middleware(['jwt:api', 'convert'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Admin Auth routes
Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('refresh', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'refresh']);
    Route::middleware(['convert'])->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'login']);
    });
    Route::middleware(['jwt:admin', 'convert'])->group(function () {
        Route::get('me', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'me']);
        Route::post('logout', [\App\Http\Controllers\Api\V1\Auth\AdminAuthController::class, 'logout']);
    });
});

// Email verification routes (public)
Route::group(['prefix' => 'v1/email-verification'], function () {
    Route::post('verify', [EmailVerificationController::class, 'verify']);
    Route::post('resend', [EmailVerificationController::class, 'resend']);
    Route::get('status', [EmailVerificationController::class, 'status']);
});

// Forgot password routes (public)
Route::group(['prefix' => 'v1/password/forgot'], function () {
    Route::post('request', [ForgotPasswordController::class, 'request']);
    Route::post('resend', [ForgotPasswordController::class, 'resend']);
    Route::post('verify', [ForgotPasswordController::class, 'verify']);
    Route::post('reset', [ForgotPasswordController::class, 'reset']);
});

// Change password routes (protected)
Route::middleware(['jwt:api', 'convert'])->group(function () {
    Route::post('v1/password/change', [ChangePasswordController::class, 'changePassword']);
});

// Upload routes
Route::middleware(['jwt:api', 'convert'])->group(function () {
    Route::post('v1/upload-temp', [ImageController::class, 'uploadTemp']);
});

// Protected routes
Route::group(['prefix' => 'v1'], function () use ($routeExcept) {
    // ADMIN ROUTEs
    Route::middleware(['jwt:admin', 'convert', 'checkPermission'])->group(function () use ($routeExcept) {

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
        Route::resource('post_catalogues', PostCatalogueController::class)->except($routeExcept); // Public read

        // Posts (Admin Write)
        Route::group(['prefix' => 'posts'], function () {
            Route::delete('bulkDelete', [PostController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostController::class, 'attach']);
            Route::delete('{id}/detach', [PostController::class, 'detach']);
            Route::post('bulkStore', [PostController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostController::class, 'bulkUpdate']);
        });
        Route::resource('posts', PostController::class)->except($routeExcept); // Public read

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
        Route::resource('post_tags', PostTagController::class)->except($routeExcept);

        // ... Add other admin resources here
    });

    // USER ROUTES (Logged In User)
    Route::middleware(['jwt:api', 'convert'])->group(function () use ($routeExcept) {

        Route::get('profile', [\App\Http\Controllers\Api\V1\User\ProfileController::class, 'show']);
        Route::put('profile', [\App\Http\Controllers\Api\V1\User\ProfileController::class, 'update']);
        Route::patch('profile', [\App\Http\Controllers\Api\V1\User\ProfileController::class, 'update']);


        // Wishlists
        Route::group(['prefix' => 'wishlists'], function () {
            Route::post('bulkStore', [WishlistController::class, 'bulkStore']);
            Route::delete('bulkDelete', [WishlistController::class, 'bulkDelete']);
        });
        Route::resource('wishlists', WishlistController::class)->except($routeExcept);

        // Address Orders
        Route::group(['prefix' => 'address_orders'], function () {
            Route::delete('bulkDelete', [AddressOrderController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AddressOrderController::class, 'attach']);
            Route::delete('{id}/detach', [AddressOrderController::class, 'detach']);
            Route::post('bulkStore', [AddressOrderController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AddressOrderController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AddressOrderController::class, 'bulkUpdate']);
        });
        Route::resource('address_orders', AddressOrderController::class)->except($routeExcept);

        // Post Comments & Likes
        Route::resource('post_comments', PostCommentController::class)->except($routeExcept);
        Route::resource('post_likes', PostLikeController::class)->except($routeExcept);
        Route::resource('post_comment_likes', PostCommentLikeController::class)->except($routeExcept);
    });

    // PUBLIC ROUTES (No Auth or Optional Auth)
    Route::middleware(['convert'])->group(function () use ($routeExcept) {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::put('products/{id}/view', [ProductController::class, 'updateView']);

        // Fix for removed resource routes
        Route::get('brands', [BrandController::class, 'index']);
        Route::get('brands/{id}', [BrandController::class, 'show']);

        Route::get('product_catalogues', [ProductCatalogueController::class, 'index']);
        Route::get('product_catalogues/{id}', [ProductCatalogueController::class, 'show']);

        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);

        Route::get('post_catalogues', [PostCatalogueController::class, 'index']);
        Route::get('post_catalogues/{id}', [PostCatalogueController::class, 'show']);

        Route::get('banners', [BannerController::class, 'index']);
        Route::get('banners/{id}', [BannerController::class, 'show']);

        Route::get('menus', [MenuController::class, 'index']);
        Route::get('menus/{id}', [MenuController::class, 'show']);

        Route::get('attribute_catalogues', [AttributeCatalogueController::class, 'index']);
        Route::get('attribute_catalogues/{id}', [AttributeCatalogueController::class, 'show']);

        Route::get('attributes', [AttributeController::class, 'index']);
        Route::get('attributes/{id}', [AttributeController::class, 'show']);

        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);
        Route::put('posts/{id}/view', [PostController::class, 'updateView']);

        Route::get('banners', [BannerController::class, 'index']);
        Route::get('banners/{id}', [BannerController::class, 'show']);

        Route::get('menus', [MenuController::class, 'index']);
        Route::get('menus/{id}', [MenuController::class, 'show']);

        Route::get('product_catalogues', [ProductCatalogueController::class, 'index']);
        Route::get('product_catalogues/{id}', [ProductCatalogueController::class, 'show']);

        Route::get('post_catalogues', [PostCatalogueController::class, 'index']);
        Route::get('post_catalogues/{id}', [PostCatalogueController::class, 'show']);

        Route::get('brands', [BrandController::class, 'index']);
        Route::get('brands/{id}', [BrandController::class, 'show']);

        Route::get('popups', [PopupController::class, 'index']);
        Route::get('popups/{id}', [PopupController::class, 'show']);

        Route::get('attribute_catalogues', [AttributeCatalogueController::class, 'index']);
        Route::get('attribute_catalogues/{id}', [AttributeCatalogueController::class, 'show']);
    });

    // FE routes (public) -> match đúng data -> route định nghĩa ở FE (phải giống route FE)
    Route::get('policy/{keyword}', [SystemController::class, 'getByKeyword']);
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::get('menu/{id}', [MenuController::class, 'show']);
    Route::get('post/{id}', [PostController::class, 'show']);
    Route::get('post/category/{id}', [PostCatalogueController::class, 'show']);
    Route::get('product/category/{id}', [ProductCatalogueController::class, 'show']);
    Route::get('agency/{id}', [AgencyController::class, 'show']);
    Route::get('faq/{id}', [FaqController::class, 'show']);
    Route::get('faq-catalogue/{id}', [FaqCatalogueController::class, 'show']);

    // This is route Cart
    Route::middleware(['convert'])->group(function () use ($routeExcept) {
        Route::group(['prefix' => 'carts'], function () {
            Route::post('bulkStore', [CartController::class, 'bulkStore']);
            Route::put('bulkUpdate', [CartController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [CartController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [CartController::class, 'bulkDelete']);
            Route::post('{id}/attach', [CartController::class, 'attach']);
            Route::delete('{id}/detach', [CartController::class, 'detach']);
        });
        Route::resource('carts', CartController::class)->except($routeExcept);

        // This is route CartItem
        Route::group(['prefix' => 'cart_items'], function () {
            Route::post('bulkStore', [CartItemController::class, 'bulkStore']);
            Route::put('bulkUpdate', [CartItemController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [CartItemController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [CartItemController::class, 'bulkDelete']);
            Route::post('{id}/attach', [CartItemController::class, 'attach']);
            Route::delete('{id}/detach', [CartItemController::class, 'detach']);
        });
        Route::resource('cart_items', CartItemController::class)->except($routeExcept);
    });


    // // Public Cart routes (guest cart)
    // Route::middleware(['convert'])->group(function () {
    //     Route::get('carts', [CartController::class, 'index']);
    //     Route::post('carts', [CartController::class, 'store']);
    //     Route::get('carts/{id}', [CartController::class, 'show']);
    //     Route::post('carts/{id}', [CartController::class, 'update']);
    //     Route::delete('carts/{id}', [CartController::class, 'destroy']);

    //     Route::post('cart_items', [CartItemController::class, 'store']);
    //     Route::post('cart_items/{id}', [CartItemController::class, 'update']);
    //     Route::delete('cart_items/{id}', [CartItemController::class, 'destroy']);
    // });
});
