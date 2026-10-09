<?php

namespace App\Services\Interfaces\ProductReview;

use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Http\Request;

interface ProductReviewServiceInterface extends BaseServiceInterface
{
    public function getByProduct(int $productId, Request $request): array;

    public function createReview(Request $request);

    public function toggleLike(int $id): array;
}
