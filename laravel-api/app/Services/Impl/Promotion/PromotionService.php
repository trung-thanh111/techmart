<?php

namespace App\Services\Impl\Promotion;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Classes\Promotion\PromotionEvaluator;
use App\Repositories\Promotion\PromotionRepository;
use App\Http\Resources\Promotion\PromotionResource;
use App\Services\Interfaces\Promotion\PromotionServiceInterface;
use App\Models\Promotion;

class PromotionService extends BaseService implements PromotionServiceInterface
{

    protected $repository;
    protected PromotionEvaluator $evaluator;

    protected $complexFilter = ['id'];

    protected $relationFilter = [
        'promotion_catalogue',
        'promotion_rules',
        'promotion_actions',
        'promotion_targets',
    ];
    protected $with = ['promotion_catalogue', 'promotion_rules', 'promotion_actions', 'promotion_targets'];

    protected $cacheKeyPrefix = 'promotions';
    protected $cacheTags = ['promotions:collection'];

    protected $useUpload = true;

    public function __construct(
        PromotionRepository $repository,
        PromotionEvaluator $evaluator
    ) {
        $this->repository = $repository;
        $this->evaluator = $evaluator;
    }

    /**
     * Calculate discount for a given cart context.
     *
     * @param array $context ['subtotal' => float, 'items' => array]
     * @return array
     */
    public function calculateDiscount(array $context): array
    {
        // Initial values for context if not present
        $context['subtotal'] = (float)($context['subtotal'] ?? 0);
        $context['items'] = $context['items'] ?? [];

        $query = Promotion::active()
            ->hasUsageLeft()
            ->with(['promotion_rules', 'promotion_actions', 'promotion_targets']);

        if (!empty($context['code'])) {
            $query->where('code', trim($context['code']));
        }

        $promotions = $query->get();

        if (!empty($context['code']) && $promotions->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn/lượt dùng.',
                'subtotal' => $context['subtotal'],
                'total_discount' => 0,
                'final_total' => $context['subtotal'],
                'applied_promotions' => [],
            ];
        }

        $result = $this->evaluator->evaluate($promotions, $context);

        if (!empty($context['code']) && empty($result['applied_promotions'])) {
            return [
                'success' => false,
                'message' => 'Đơn hàng chưa thỏa mãn điều kiện áp dụng mã giảm giá này.',
                'subtotal' => $context['subtotal'],
                'total_discount' => 0,
                'final_total' => $context['subtotal'],
                'applied_promotions' => [],
            ];
        }

        $result['success'] = true;
        return $result;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self
    {
        $this->initializeBasicModelData($request);

        // Upload single image if present
        $this->uploadImage($request, 'image', 'default', 'image');

        // Normalize combinable_with: accept JSON string or array
        if ($request->has('combinable_with')) {
            $combinable = $request->input('combinable_with');
            if (is_string($combinable)) {
                $decoded = json_decode($combinable, true);
                $this->modelData['combinable_with'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            } else {
                $this->modelData['combinable_with'] = $combinable;
            }
        }

        return $this;
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    /**
     * Xử lý quan hệ nested rules/actions/targets riêng cho Promotion.
     *
     * @param Request $request
     * @return $this
     */
    protected function handleRelation(Request $request): self
    {
        // Nếu không có model (lỗi) thì bỏ qua
        if (!$this->model || !$this->model->getKey()) {
            return $this;
        }

        // Rules
        if ($request->has('rules')) {
            $this->model->promotion_rules()->delete();
            $rules = $request->input('rules', []);
            foreach ($rules as $rule) {
                $this->model->promotion_rules()->create($rule);
            }
        }

        // Actions
        if ($request->has('actions')) {
            $this->model->promotion_actions()->delete();
            $actions = $request->input('actions', []);
            foreach ($actions as $action) {
                $this->model->promotion_actions()->create($action);
            }
        }

        // Targets
        if ($request->has('targets')) {
            $this->model->promotion_targets()->delete();
            $targets = $request->input('targets', []);
            foreach ($targets as $target) {
                $this->model->promotion_targets()->create($target);
            }
        }

        return $this;
    }

    /**
     * Kiểm tra chương trình promotion đang active.
     *
     * @param Promotion $promotion
     * @return bool
     */
    public function isActive(Promotion $promotion): bool
    {
        if ((int)$promotion->publish !== 2) {
            return false;
        }
        $now = now();
        if ($promotion->start_date && $now->lt($promotion->start_date)) {
            return false;
        }
        if ($promotion->end_date && $now->gt($promotion->end_date)) {
            return false;
        }
        return true;
    }

    /**
     * Kiểm tra khách hàng có thể dùng promotion hay không.
     *
     * @param Promotion $promotion
     * @param \App\Models\User|int|null $customer
     * @return bool
     */
    public function canBeUsedByCustomer(Promotion $promotion, $customer = null): bool
    {
        if (!$this->isActive($promotion)) {
            return false;
        }
        if ($promotion->usage_limit !== null && $promotion->used_count >= $promotion->usage_limit) {
            return false;
        }
        if ($customer) {
            $customerId = is_object($customer) ? $customer->id : (int)$customer;
            if ($promotion->usage_per_customer !== null) {
                $usedByCustomer = $promotion->promotion_usages()->where('customer_id', $customerId)->count();
                if ($usedByCustomer >= $promotion->usage_per_customer) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Kiểm tra có thể kết hợp với promotion khác không.
     *
     * @param Promotion $promotion
     * @param int|null $otherPromotionId
     * @return bool
     */
    public function canCombineWith(Promotion $promotion, ?int $otherPromotionId): bool
    {
        if (!$promotion->can_combine) {
            return false;
        }
        if (empty($promotion->combinable_with)) {
            return true;
        }
        return in_array($otherPromotionId, $promotion->combinable_with, true);
    }

    /**
     * Lấy usage của khách hàng cho promotion này.
     *
     * @param Promotion $promotion
     * @param \App\Models\User|int $customer
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getUsageByCustomer(Promotion $promotion, $customer)
    {
        $customerId = is_object($customer) ? $customer->id : (int)$customer;
        return $promotion->promotion_usages()->where('customer_id', $customerId)->first();
    }

    /**
     * Tính số lần còn lại có thể dùng.
     *
     * @param Promotion $promotion
     * @return int|null
     */
    public function getRemainingUsage(Promotion $promotion): ?int
    {
        if ($promotion->usage_limit === null) {
            return null;
        }
        $remaining = (int)$promotion->usage_limit - (int)$promotion->used_count;
        return $remaining < 0 ? 0 : $remaining;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getResource(Model $resource)
    {
        return new PromotionResource($resource);
    }

    public function paginate(Request $request)
    {
        if (!$request->has('simple')) {
            $request->merge(['simple' => true]);
        }
        return parent::paginate($request);
    }
}
