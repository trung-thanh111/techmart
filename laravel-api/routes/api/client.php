<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Menu\MenuController;
use App\Http\Controllers\Api\V1\System\PopupController;
use App\Http\Controllers\Api\V1\System\SystemController;
use App\Http\Controllers\Api\V1\Cart\CartItemController;
use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\Product\WishlistController;
use App\Http\Controllers\Api\V1\Post\PostCommentLikeController;
use App\Http\Controllers\Api\V1\Post\PostLikeController;
use App\Http\Controllers\Api\V1\Post\PostCommentController;
use App\Http\Controllers\Api\V1\Promotion\PromotionController;
use App\Http\Controllers\Api\V1\Product\ProductTagController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Product\ProductCatalogueController;
use App\Http\Controllers\Api\V1\Brand\BrandController;
use App\Http\Controllers\Api\V1\Attribute\AttributeController;
use App\Http\Controllers\Api\V1\Attribute\AttributeCatalogueController;
use App\Http\Controllers\Api\V1\Post\PostController;
use App\Http\Controllers\Api\V1\Post\PostCatalogueController;
use App\Http\Controllers\Api\V1\Post\TagController as PostTagController;
use App\Http\Controllers\Api\V1\Faq\FaqController;
use App\Http\Controllers\Api\V1\Faq\FaqCatalogueController;
use App\Http\Controllers\Api\V1\AgencySystem\AgencyController;
use App\Http\Controllers\Api\V1\Image\ImageController;
use App\Http\Controllers\Api\V1\User\AddressOrderController;
use App\Http\Controllers\Api\V1\Banner\BannerController;
use App\Http\Controllers\Api\V1\EmailVerification\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\ChangePasswordController;
use App\Http\Controllers\Api\V1\Order\OrderController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Payment\PaymentWebhookController;

$routeExcept = ['create', 'edit'];

// Client Auth routes
Route::group(['prefix' => 'v1/auth'], function () {
    // Public routes (no auth required)
    Route::middleware(['convert'])->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // Refresh token (no auth required, but needs refresh token cookie)
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Protected routes (requires JWT auth)
    Route::middleware(['jwt:api', 'convert'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::group(['prefix' => 'v1/email-verification'], function () {
    Route::post('verify', [EmailVerificationController::class, 'verify']);
    Route::post('resend', [EmailVerificationController::class, 'resend']);
    Route::get('status', [EmailVerificationController::class, 'status']);
});

Route::group(['prefix' => 'v1/password/forgot'], function () {
    Route::post('request', [ForgotPasswordController::class, 'request']);
    Route::post('resend', [ForgotPasswordController::class, 'resend']);
    Route::post('verify', [ForgotPasswordController::class, 'verify']);
    Route::post('reset', [ForgotPasswordController::class, 'reset']);
});

Route::middleware(['jwt:api', 'convert'])->group(function () {
    Route::post('v1/password/change', [ChangePasswordController::class, 'changePassword']);
    Route::post('v1/upload-temp', [ImageController::class, 'uploadTemp']);
});
// Protected routes group v1
Route::group(['prefix' => 'v1'], function () use ($routeExcept) {

    // USER ROUTES (Logged In User)
    Route::middleware(['jwt:api', 'convert'])->group(function () use ($routeExcept) {

        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::patch('profile', [ProfileController::class, 'update']);

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

        // Orders
        Route::group(['prefix' => 'orders'], function () {
            Route::delete('bulkDelete', [OrderController::class, 'bulkDelete']);
            Route::post('{id}/attach', [OrderController::class, 'attach']);
            Route::delete('{id}/detach', [OrderController::class, 'detach']);
            Route::post('bulkStore', [OrderController::class, 'bulkStore']);
            Route::put('bulkUpdate', [OrderController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [OrderController::class, 'bulkUpdate']);
            Route::post('{id}/cancel', [OrderController::class, 'cancel']);
        });
        Route::resource('orders', OrderController::class)->except($routeExcept);

        // Post Comments & Likes
        Route::resource('post_comments', PostCommentController::class)->except(array_merge($routeExcept, ['index']));
        Route::resource('post_likes', PostLikeController::class)->except(array_merge($routeExcept, ['index']));
        Route::resource('post_comment_likes', PostCommentLikeController::class)->except(array_merge($routeExcept, ['index']));
    });

    // PUBLIC ROUTES (No Auth or Optional Auth)
    Route::middleware(['convert'])->group(function () use ($routeExcept) {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::put('products/{id}/view', [ProductController::class, 'updateView']);

        Route::get('brands', [BrandController::class, 'index']);
        Route::get('brands/{id}', [BrandController::class, 'show']);

        Route::get('agencies', [AgencyController::class, 'index']);
        Route::get('agencies/{id}', [AgencyController::class, 'show']);

        Route::get('systems', [SystemController::class, 'index']);
        Route::get('systems/{id}', [SystemController::class, 'show']);

        Route::get('product_catalogues', [ProductCatalogueController::class, 'index']);
        Route::get('product_catalogues/{id}', [ProductCatalogueController::class, 'show']);

        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{id}', [PostController::class, 'show']);
        Route::put('posts/{id}/view', [PostController::class, 'updateView']);

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

        Route::get('popups', [PopupController::class, 'index']);
        Route::get('popups/{id}', [PopupController::class, 'show']);

        Route::get('post_tags', [PostTagController::class, 'index']);
        Route::get('post_tags', [PostTagController::class, 'index']);
        Route::get('product_tags', [ProductTagController::class, 'index']);

        // Public Comments (View only)
        Route::get('post_comments', [PostCommentController::class, 'index']);
        Route::get('post_likes', [PostLikeController::class, 'index']);
        Route::get('post_comment_likes', [PostCommentLikeController::class, 'index']);
        Route::post('promotion/apply_discount', [PromotionController::class, 'applyDiscount']);
    });

    // FE routes (public matchers)
    Route::get('policy/{keyword}', [SystemController::class, 'getByKeyword']);
    Route::get('systems', [SystemController::class, 'index']); // Added for systemService.all/findByKeyword usage
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::get('menu/{id}', [MenuController::class, 'show']);
    Route::get('post/{id}', [PostController::class, 'show']);
    Route::put('posts/{id}/view', [PostController::class, 'updateView']);
    Route::get('post/category/{id}', [PostCatalogueController::class, 'show']);
    Route::get('product/category/{id}', [ProductCatalogueController::class, 'show']);
    Route::get('agency/{id}', [AgencyController::class, 'show']);
    Route::get('faq/{id}', [FaqController::class, 'show']);
    Route::get('faq_catalogues', [FaqCatalogueController::class, 'index']);
    Route::get('faq_catalogues/{id}', [FaqCatalogueController::class, 'show']);

    // Cart Routes
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

        // CartItem
        Route::group(['prefix' => 'cart_items'], function () {
            Route::post('bulkStore', [CartItemController::class, 'bulkStore']);
            Route::put('bulkUpdate', [CartItemController::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [CartItemController::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [CartItemController::class, 'bulkDelete']);
            Route::post('{id}/attach', [CartItemController::class, 'attach']);
            Route::delete('{id}/detach', [CartItemController::class, 'detach']);
        });
        Route::resource('cart_items', CartItemController::class)->except($routeExcept);

        // Payment Routes
        Route::group(['prefix' => 'payment'], function () {
            Route::post('create', [PaymentController::class, 'create']);
            Route::get('{orderId}', [PaymentController::class, 'show']);
            Route::post('refund', [PaymentController::class, 'refund']);
        });
    });
});

// Payment Webhook Routes (public — no auth, verify by signature)
Route::group(['prefix' => 'v1/payment/webhook'], function () {
    Route::post('momo', [PaymentWebhookController::class, 'handleMoMo']);
    Route::get('vnpay', [PaymentWebhookController::class, 'handleVNPay']); // VNPay IPN là GET
});

// VNPay Return URL — user redirect back sau thanh toán
Route::get('v1/payment/vnpay-return', [PaymentWebhookController::class, 'vnpayReturn']);

// Payment Health Check (public)
Route::get('v1/payment/health', [PaymentController::class, 'health']);
