<?php

use Illuminate\Support\Facades\Route;
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
    Route::middleware(['jwt:api', 'convert', 'checkPermission'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
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
    Route::middleware(['jwt:api', 'convert', 'checkPermission'])->group(function () use ($routeExcept) {

        // Attribute Catalogue routes
        Route::group(['prefix' => 'attribute_catalogues'], function () {
            Route::delete('bulkDelete', [AttributeCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AttributeCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [AttributeCatalogueController::class, 'detach']);
            Route::post('bulkStore', [AttributeCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AttributeCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AttributeCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('attribute_catalogues', AttributeCatalogueController::class)->except($routeExcept);

        // Attribute routes
        Route::group(['prefix' => 'attributes'], function () {
            Route::delete('bulkDelete', [AttributeController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AttributeController::class, 'attach']);
            Route::delete('{id}/detach', [AttributeController::class, 'detach']);
            Route::post('bulkStore', [AttributeController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AttributeController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AttributeController::class, 'bulkUpdate']);
        });
        Route::resource('attributes', AttributeController::class)->except($routeExcept);

        // Permission routes
        Route::group(['prefix' => 'permissions'], function () {
            Route::delete('bulkDelete', [PermissionController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PermissionController::class, 'attach']);
            Route::delete('{id}/detach', [PermissionController::class, 'detach']);
            Route::post('bulkStore', [PermissionController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PermissionController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PermissionController::class, 'bulkUpdate']);
        });
        Route::resource('permissions', PermissionController::class)->except($routeExcept);

        // Post Catalogue routes
        Route::group(['prefix' => 'post_catalogues'], function () {
            Route::delete('bulkDelete', [PostCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [PostCatalogueController::class, 'detach']);
            Route::post('bulkStore', [PostCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('post_catalogues', PostCatalogueController::class)->except($routeExcept);

        // Post routes
        Route::group(['prefix' => 'posts'], function () {
            Route::delete('bulkDelete', [PostController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostController::class, 'attach']);
            Route::delete('{id}/detach', [PostController::class, 'detach']);
            Route::post('bulkStore', [PostController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostController::class, 'bulkUpdate']);
        });
        Route::resource('posts', PostController::class)->except($routeExcept);

        // Product Catalogue routes
        Route::group(['prefix' => 'product_catalogues'], function () {
            Route::delete('bulkDelete', [ProductCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [ProductCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [ProductCatalogueController::class, 'detach']);
            Route::post('bulkStore', [ProductCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [ProductCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [ProductCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('product_catalogues', ProductCatalogueController::class)->except($routeExcept);

        // Product routes
        Route::group(['prefix' => 'products'], function () {
            Route::delete('bulkDelete', [ProductController::class, 'bulkDelete']);
            Route::post('{id}/attach', [ProductController::class, 'attach']);
            Route::delete('{id}/detach', [ProductController::class, 'detach']);
            Route::post('bulkStore', [ProductController::class, 'bulkStore']);
            Route::put('bulkUpdate', [ProductController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [ProductController::class, 'bulkUpdate']);
        });
        Route::resource('products', ProductController::class)->except($routeExcept);

        // Promotion Catalogue routes
        Route::group(['prefix' => 'promotion_catalogues'], function () {
            Route::delete('bulkDelete', [PromotionCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PromotionCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [PromotionCatalogueController::class, 'detach']);
            Route::post('bulkStore', [PromotionCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PromotionCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PromotionCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('promotion_catalogues', PromotionCatalogueController::class)->except($routeExcept);


        // User Catalogue routes
        Route::group(['prefix' => 'user_catalogues'], function () {
            Route::delete('bulkDelete', [UserCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [UserCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [UserCatalogueController::class, 'detach']);
            Route::post('bulkStore', [UserCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [UserCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [UserCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('user_catalogues', UserCatalogueController::class)->except($routeExcept);

        // User routes
        Route::group(['prefix' => 'users'], function () {
            Route::delete('bulkDelete', [UserController::class, 'bulkDelete']);
            Route::post('{id}/attach', [UserController::class, 'attach']);
            Route::delete('{id}/detach', [UserController::class, 'detach']);
            Route::post('bulkStore', [UserController::class, 'bulkStore']);
            Route::put('bulkUpdate', [UserController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [UserController::class, 'bulkUpdate']);
        });
        Route::resource('users', UserController::class)->except($routeExcept);

        // Brand routes
        Route::group(['prefix' => 'brands'], function () {
            Route::delete('bulkDelete', [BrandController::class, 'bulkDelete']);
            Route::post('{id}/attach', [BrandController::class, 'attach']);
            Route::delete('{id}/detach', [BrandController::class, 'detach']);
            Route::post('bulkStore', [BrandController::class, 'bulkStore']);
            Route::put('bulkUpdate', [BrandController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [BrandController::class, 'bulkUpdate']);
        });
        Route::resource('brands', BrandController::class)->except($routeExcept);

        // Product Variant routes
        Route::group(['prefix' => 'product_variants'], function () {
            Route::delete('bulkDelete', [ProductVariantController::class, 'bulkDelete']);
            Route::post('{id}/attach', [ProductVariantController::class, 'attach']);
            Route::delete('{id}/detach', [ProductVariantController::class, 'detach']);
            Route::post('bulkStore', [ProductVariantController::class, 'bulkStore']);
            Route::put('bulkUpdate', [ProductVariantController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [ProductVariantController::class, 'bulkUpdate']);
        });
        Route::resource('product_variants', ProductVariantController::class)->except($routeExcept);

        // Post Tag routes
        Route::group(['prefix' => 'post_tags'], function () {
            Route::delete('bulkDelete', [PostTagController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostTagController::class, 'attach']);
            Route::delete('{id}/detach', [PostTagController::class, 'detach']);
            Route::post('bulkStore', [PostTagController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostTagController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostTagController::class, 'bulkUpdate']);
        });
        Route::resource('post_tags', PostTagController::class)->except($routeExcept);

        // FAQ routes
        Route::group(['prefix' => 'faqs'], function () {
            Route::delete('bulkDelete', [FaqController::class, 'bulkDelete']);
            Route::post('{id}/attach', [FaqController::class, 'attach']);
            Route::delete('{id}/detach', [FaqController::class, 'detach']);
            Route::post('bulkStore', [FaqController::class, 'bulkStore']);
            Route::put('bulkUpdate', [FaqController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [FaqController::class, 'bulkUpdate']);
        });
        Route::resource('faqs', FaqController::class)->except($routeExcept);

        // FAQ Catalogue routes
        Route::group(['prefix' => 'faq_catalogues'], function () {
            Route::delete('bulkDelete', [FaqCatalogueController::class, 'bulkDelete']);
            Route::post('{id}/attach', [FaqCatalogueController::class, 'attach']);
            Route::delete('{id}/detach', [FaqCatalogueController::class, 'detach']);
            Route::post('bulkStore', [FaqCatalogueController::class, 'bulkStore']);
            Route::put('bulkUpdate', [FaqCatalogueController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [FaqCatalogueController::class, 'bulkUpdate']);
        });
        Route::resource('faq_catalogues', FaqCatalogueController::class)->except($routeExcept);

        // Agency routes
        Route::group(['prefix' => 'agencies'], function () {
            Route::delete('bulkDelete', [AgencyController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AgencyController::class, 'attach']);
            Route::delete('{id}/detach', [AgencyController::class, 'detach']);
            Route::post('bulkStore', [AgencyController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AgencyController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AgencyController::class, 'bulkUpdate']);
        });
        Route::resource('agencies', AgencyController::class)->except($routeExcept);

        // Agency Working Hour routes
        Route::group(['prefix' => 'agency_working_hours'], function () {
            Route::delete('bulkDelete', [AgencyWorkingHourController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AgencyWorkingHourController::class, 'attach']);
            Route::delete('{id}/detach', [AgencyWorkingHourController::class, 'detach']);
            Route::post('bulkStore', [AgencyWorkingHourController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AgencyWorkingHourController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AgencyWorkingHourController::class, 'bulkUpdate']);
        });
        Route::resource('agency_working_hours', AgencyWorkingHourController::class)->except($routeExcept);

        // Image routes
        Route::group(['prefix' => 'images'], function () {
            Route::delete('bulkDelete', [ImageController::class, 'bulkDelete']);
            Route::post('{id}/attach', [ImageController::class, 'attach']);
            Route::delete('{id}/detach', [ImageController::class, 'detach']);
            Route::post('bulkStore', [ImageController::class, 'bulkStore']);
            Route::put('bulkUpdate', [ImageController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [ImageController::class, 'bulkUpdate']);
        });
        Route::resource('images', ImageController::class)->except($routeExcept);

        // Mail routes
        Route::group(['prefix' => 'mails'], function () {
            Route::delete('bulkDelete', [MailController::class, 'bulkDelete']);
            Route::post('{id}/attach', [MailController::class, 'attach']);
            Route::delete('{id}/detach', [MailController::class, 'detach']);
            Route::post('bulkStore', [MailController::class, 'bulkStore']);
            Route::put('bulkUpdate', [MailController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [MailController::class, 'bulkUpdate']);
        });
        Route::resource('mails', MailController::class)->except($routeExcept);

        // Address Order routes
        Route::group(['prefix' => 'address_orders'], function () {
            Route::delete('bulkDelete', [AddressOrderController::class, 'bulkDelete']);
            Route::post('{id}/attach', [AddressOrderController::class, 'attach']);
            Route::delete('{id}/detach', [AddressOrderController::class, 'detach']);
            Route::post('bulkStore', [AddressOrderController::class, 'bulkStore']);
            Route::put('bulkUpdate', [AddressOrderController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [AddressOrderController::class, 'bulkUpdate']);
        });
        Route::resource('address_orders', AddressOrderController::class)->except($routeExcept);

        // Banner routes
        Route::group(['prefix' => 'banners'], function () {
            Route::delete('bulkDelete', [BannerController::class, 'bulkDelete']);
            Route::post('{id}/attach', [BannerController::class, 'attach']);
            Route::delete('{id}/detach', [BannerController::class, 'detach']);
            Route::post('bulkStore', [BannerController::class, 'bulkStore']);
            Route::put('bulkUpdate', [BannerController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [BannerController::class, 'bulkUpdate']);
        });
        Route::resource('banners', BannerController::class)->except($routeExcept);

        // This is route ProductTag
        Route::group(['prefix' => 'product_tags'], function () {
            Route::post('bulkStore', [ProductTagController::class, 'bulkStore']);
            Route::put('bulkUpdate', [ProductTagController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [ProductTagController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [ProductTagController::class, 'bulkDelete']);
            Route::post('{id}/attach', [ProductTagController::class, 'attach']);
            Route::delete('{id}/detach', [ProductTagController::class, 'detach']);
        });
        Route::resource('product_tags', ProductTagController::class)->except($routeExcept);

        // This is route PostComment
        Route::group(['prefix' => 'post_comments'], function () {
            Route::post('bulkStore', [PostCommentController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostCommentController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostCommentController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [PostCommentController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostCommentController::class, 'attach']);
            Route::delete('{id}/detach', [PostCommentController::class, 'detach']);
        });
        Route::resource('post_comments', PostCommentController::class)->except($routeExcept);

        // This is route PostLike
        Route::group(['prefix' => 'post_likes'], function () {
            Route::post('bulkStore', [PostLikeController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostLikeController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostLikeController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [PostLikeController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostLikeController::class, 'attach']);
            Route::delete('{id}/detach', [PostLikeController::class, 'detach']);
        });
        Route::resource('post_likes', PostLikeController::class)->except($routeExcept);

        // This is route PostCommentLike
        Route::group(['prefix' => 'post_comment_likes'], function () {
            Route::post('bulkStore', [PostCommentLikeController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PostCommentLikeController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PostCommentLikeController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [PostCommentLikeController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PostCommentLikeController::class, 'attach']);
            Route::delete('{id}/detach', [PostCommentLikeController::class, 'detach']);
        });
        Route::resource('post_comment_likes', PostCommentLikeController::class)->except($routeExcept);

        // This is route Promotion
        Route::group(['prefix' => 'promotions'], function () {
            Route::post('bulkStore', [PromotionController::class, 'bulkStore']);
            Route::put('bulkUpdate', [PromotionController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [PromotionController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [PromotionController::class, 'bulkDelete']);
            Route::post('{id}/attach', [PromotionController::class, 'attach']);
            Route::delete('{id}/detach', [PromotionController::class, 'detach']);
        });
        Route::resource('promotions', PromotionController::class)->except($routeExcept);

        // This is route BannerPage
        Route::group(['prefix' => 'banner_pages'], function () {
            Route::post('bulkStore', [BannerPageController::class, 'bulkStore']);
            Route::put('bulkUpdate', [BannerPageController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [BannerPageController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [BannerPageController::class, 'bulkDelete']);
            Route::post('{id}/attach', [BannerPageController::class, 'attach']);
            Route::delete('{id}/detach', [BannerPageController::class, 'detach']);
        });
        Route::resource('banner_pages', BannerPageController::class)->except($routeExcept);

        // This is route BannerPosition
        Route::group(['prefix' => 'banner_positions'], function () {
            Route::post('bulkStore', [BannerPositionController::class, 'bulkStore']);
            Route::put('bulkUpdate', [BannerPositionController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [BannerPositionController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [BannerPositionController::class, 'bulkDelete']);
            Route::post('{id}/attach', [BannerPositionController::class, 'attach']);
            Route::delete('{id}/detach', [BannerPositionController::class, 'detach']);
        });
        Route::resource('banner_positions', BannerPositionController::class)->except($routeExcept);

        // This is route Inventory
        Route::group(['prefix' => 'inventorys'], function() {
            Route::post('bulkStore', [InventoryController::class, 'bulkStore']);
            Route::put('bulkUpdate', [InventoryController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [InventoryController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [InventoryController::class, 'bulkDelete']);
            Route::post('{id}/attach', [InventoryController::class, 'attach']);
            Route::delete('{id}/detach', [InventoryController::class, 'detach']);
        });
        Route::resource('inventorys', InventoryController::class)->except($routeExcept);

        // generate route

    });

    // FE routes (public) -> match đúng data -> route định nghĩa ở FE (phải giống route FE)
    Route::get('product/{slug}', [ProductController::class, 'showBySlug']);
    Route::get('post/{slug}', [PostController::class, 'showBySlug']);
    Route::get('post/category/{slug}', [PostCatalogueController::class, 'showBySlug']);
    Route::get('agency/{slug}', [AgencyController::class, 'showBySlug']);
    Route::get('faq/{slug}', [FaqController::class, 'showBySlug']);
    Route::get('faq-catalogue/{slug}', [FaqCatalogueController::class, 'showBySlug']);
});
