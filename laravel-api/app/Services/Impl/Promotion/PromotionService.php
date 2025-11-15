<?php

namespace App\Services\Impl\Promotion;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Promotion\PromotionRepository;
use App\Http\Resources\Promotion\PromotionResource;
use App\Services\Interfaces\Promotion\PromotionServiceInterface;
use App\Models\PromotionTarget;

class PromotionService extends BaseService implements PromotionServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = ['promotion_targets', 'promotion_catalogues'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'promotions';
    protected $cacheTags = ['promotions:collection'];

    protected $useUpload = false;

    public function __construct(
        PromotionRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
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

    protected function handleRelation(Request $request): self
    {
        // Xử lý promotion_targets
        if ($request->has('promotionTargets')) {
            $this->handlePromotionTargets($request);
        }

        return $this;
    }

    private function handlePromotionTargets(Request $request): self
    {
        $targets = $request->input('promotionTargets');

        if (!is_array($targets) || empty($targets)) {
            // Nếu không có targets, xóa tất cả targets cũ
            if ($this->model->exists) {
                $this->model->promotion_targets()->delete();
            }
            return $this;
        }

        // Xóa tất cả targets cũ
        if ($this->model->exists) {
            $this->model->promotion_targets()->delete();
        }

        // Tạo targets mới
        foreach ($targets as $targetData) {
            $targetAttributes = [
                'promotion_id' => $this->model->id,
                'product_id' => $targetData['productId'] ?? null,
                'product_variant_id' => $targetData['productVariantId'] ?? null,
                'product_catalogue_id' => $targetData['productCatalogueId'] ?? null,
                'is_gift' => $targetData['isGift'] ?? false,
                'min_quantity' => $targetData['minQuantity'] ?? 1,
                'max_quantity' => $targetData['maxQuantity'] ?? null,
            ];

            PromotionTarget::create($targetAttributes);
        }

        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new PromotionResource($resource);
    }
}
