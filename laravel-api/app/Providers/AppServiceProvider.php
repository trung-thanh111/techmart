<?php
namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\Post\PostCommentLikeServiceInterface;
use App\Services\Impl\Post\PostCommentLikeService;
use App\Services\Interfaces\Post\PostLikeServiceInterface;
use App\Services\Impl\Post\PostLikeService;
use App\Services\Interfaces\Post\PostCommentServiceInterface;
use App\Services\Impl\Post\PostCommentService;
use App\Services\Interfaces\Product\ProductTagServiceInterface;
use App\Services\Impl\Product\ProductTagService;
use App\Services\Interfaces\Banner\BannerServiceInterface;
use App\Services\Impl\Banner\BannerService;
use App\Services\Interfaces\User\AddressOrderServiceInterface;
use App\Services\Impl\User\AddressOrderService;
use App\Services\Interfaces\Faq\FaqServiceInterface;
use App\Services\Impl\Faq\FaqService;
use App\Services\Interfaces\Faq\FaqCatalogueServiceInterface;
use App\Services\Impl\Faq\FaqCatalogueService;
use App\Services\Interfaces\AgencySystem\AgencyWorkingHourServiceInterface;
use App\Services\Impl\AgencySystem\AgencyWorkingHourService;
use App\Services\Interfaces\AgencySystem\AgencyServiceInterface;
use App\Services\Impl\AgencySystem\AgencyService;
use App\Services\Interfaces\Post\TagServiceInterface as PostTagServiceInterface;
use App\Services\Impl\Post\TagService as PostTagService;
use App\Services\Interfaces\Promotion\PromotionCatalogueServiceInterface;
use App\Services\Impl\Promotion\PromotionCatalogueService;
use App\Services\Interfaces\Product\ProductVariantServiceInterface;
use App\Services\Impl\Product\ProductVariantService;
use App\Services\Interfaces\Product\ProductServiceInterface;
use App\Services\Impl\Product\ProductService;
use App\Services\Interfaces\Attribute\AttributeServiceInterface;
use App\Services\Impl\Attribute\AttributeService;
use App\Services\Interfaces\Attribute\AttributeCatalogueServiceInterface;
use App\Services\Impl\Attribute\AttributeCatalogueService;
use App\Services\Interfaces\Product\ProductCatalogueServiceInterface;
use App\Services\Impl\Product\ProductCatalogueService;
use App\Services\Interfaces\Brand\BrandServiceInterface;
use App\Services\Impl\Brand\BrandService;
use App\Services\Interfaces\Post\PostServiceInterface;
use App\Services\Impl\Post\PostService;
use App\Services\Interfaces\Post\PostCatalogueServiceInterface;
use App\Services\Impl\Post\PostCatalogueService;
use App\Services\Interfaces\Permission\PermissionServiceInterface;
use App\Services\Impl\Permission\PermissionService;
use App\Services\Interfaces\User\UserServiceInterface;
use App\Services\Impl\User\UserService;
use App\Services\Impl\Auth\AuthService;
use App\Services\Impl\ImageService;
use App\Services\Impl\User\UserCatalogueService;
use App\Services\Interfaces\Auth\AuthServiceInterface;
use App\Services\Interfaces\ImageServiceInterface;
use App\Services\Interfaces\User\UserCatalogueServiceInterface;
use App\Services\Interfaces\Mail\MailServiceInterface;
use App\Services\Impl\Mail\MailService;
use App\Services\Interfaces\EmailVerification\EmailVerificationServiceInterface;
use App\Services\Impl\EmailVerification\EmailVerificationService;
use App\Services\Interfaces\PasswordReset\PasswordResetServiceInterface;
use App\Services\Impl\PasswordReset\PasswordResetService;
use App\Services\Interfaces\Auth\ChangePasswordServiceInterface;
use App\Services\Impl\Auth\ChangePasswordService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the service bindings.
     */
    protected array $bindServices = [
        AuthServiceInterface::class => AuthService::class,
        UserCatalogueServiceInterface::class => UserCatalogueService::class,
        UserServiceInterface::class => UserService::class,
        PermissionServiceInterface::class => PermissionService::class,
        ImageServiceInterface::class => ImageService::class,
        PostCatalogueServiceInterface::class => PostCatalogueService::class,
        PostServiceInterface::class => PostService::class,
        BrandServiceInterface::class => BrandService::class,
        ProductCatalogueServiceInterface::class => ProductCatalogueService::class,
        AttributeCatalogueServiceInterface::class => AttributeCatalogueService::class,
        AttributeServiceInterface::class => AttributeService::class,
        ProductServiceInterface::class => ProductService::class,
        ProductVariantServiceInterface::class => ProductVariantService::class,
        PromotionCatalogueServiceInterface::class => PromotionCatalogueService::class,
        PostTagServiceInterface::class => PostTagService::class,
        AgencyServiceInterface::class => AgencyService::class,
        AgencyWorkingHourServiceInterface::class => AgencyWorkingHourService::class,
        FaqCatalogueServiceInterface::class => FaqCatalogueService::class,
        FaqServiceInterface::class => FaqService::class,
        AddressOrderServiceInterface::class => AddressOrderService::class,
        BannerServiceInterface::class => BannerService::class,
        ProductTagServiceInterface::class => ProductTagService::class,
        PostCommentServiceInterface::class => PostCommentService::class,
        PostLikeServiceInterface::class => PostLikeService::class,
        PostCommentLikeServiceInterface::class => PostCommentLikeService::class,
        MailServiceInterface::class => MailService::class,
        EmailVerificationServiceInterface::class => EmailVerificationService::class,
        PasswordResetServiceInterface::class => PasswordResetService::class,
        ChangePasswordServiceInterface::class => ChangePasswordService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->bindServices as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
    }
}
