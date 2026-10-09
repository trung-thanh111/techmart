<?php

namespace App\Services\Impl\ProductReview;

use App\Http\Resources\ProductReview\ProductReviewResource;
use App\Models\ProductReview;
use App\Repositories\ProductReview\ProductReviewRepository;
use App\Services\Impl\BaseService;
use App\Services\Interfaces\ProductReview\ProductReviewServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewService extends BaseService implements ProductReviewServiceInterface
{
    protected $repository;

    protected $complexFilter = ['id'];
    protected $simpleFilter = ['publish', 'product_id', 'rating', 'user_id'];
    protected $fieldSearchs = ['user_name', 'user_email', 'comment'];
    protected $with = ['user'];

    protected $cacheKeyPrefix = 'product_reviews';
    protected $cacheTags = ['product_reviews:collection'];

    public function __construct(ProductReviewRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    public function getByProduct(int $productId, Request $request): array
    {
        $baseQuery = ProductReview::where('product_id', $productId)->where('publish', 2);

        // Tính toán thống kê đánh giá
        $totalReviews = (clone $baseQuery)->count();
        $avgRating = $totalReviews > 0 ? (clone $baseQuery)->avg('rating') : 0;

        $starCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        if ($totalReviews > 0) {
            $counts = (clone $baseQuery)
                ->select('rating', DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            foreach ($starCounts as $star => $v) {
                $starCounts[$star] = $counts[$star] ?? 0;
            }
        }

        $starPercentages = [];
        foreach ($starCounts as $star => $count) {
            $starPercentages[$star] = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0;
        }

        // Lọc theo rating nếu có
        $query = (clone $baseQuery)->with('user');
        if ($filterRating = $request->input('rating')) {
            $query->where('rating', (int) $filterRating);
        }

        // Sắp xếp
        $sortBy = $request->input('sort', 'latest');
        if ($sortBy === 'highest') {
            $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'lowest') {
            $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $limit = max(1, min((int) $request->input('limit', 10), 50));
        $paginated = $query->paginate($limit);

        return [
            'summary' => [
                'total_reviews' => $totalReviews,
                'average_rating' => round($avgRating, 1),
                'star_counts' => $starCounts,
                'star_percentages' => $starPercentages,
            ],
            'reviews' => ProductReviewResource::collection($paginated->items()),
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ];
    }

    public function createReview(Request $request)
    {
        $reviewData = $request->only([
            'product_id',
            'user_id',
            'user_name',
            'user_email',
            'user_avatar',
            'rating',
            'comment',
            'images',
            'publish',
        ]);

        $review = ProductReview::create($reviewData);

        $this->clearCacheByTags();

        return new ProductReviewResource($review);
    }

    public function toggleLike(int $id): array
    {
        $review = ProductReview::findOrFail($id);
        $review->increment('likes_count');

        return [
            'id' => $review->id,
            'likes_count' => $review->likes_count,
        ];
    }

    protected function prepareModelData(Request $request): self
    {
        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new ProductReviewResource($resource);
    }
}
