<?php

namespace App\Http\Controllers\Api\V1\ProductReview;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\ProductReview\StoreProductReviewRequest;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\ProductReview\ProductReviewServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class ProductReviewController extends BaseController
{
    protected $service;

    public function __construct(ProductReviewServiceInterface $service)
    {
        $this->service = $service;
    }

    protected function getService()
    {
        return $this->service;
    }

    /**
     * Lấy danh sách đánh giá kèm thống kê của 1 sản phẩm
     */
    public function getByProduct(Request $request, int $productId): JsonResponse
    {
        $data = $this->service->getByProduct($productId, $request);
        return ApiResource::ok($data, Lang::get('message.successfully'));
    }

    /**
     * Tạo đánh giá mới cho sản phẩm
     */
    public function storeReview(StoreProductReviewRequest $request): JsonResponse
    {
        $review = $this->service->createReview($request);
        return ApiResource::ok($review, 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
    }

    /**
     * Thích đánh giá
     */
    public function like(Request $request, int $id): JsonResponse
    {
        $data = $this->service->toggleLike($id);
        return ApiResource::ok($data, Lang::get('message.successfully'));
    }
}
