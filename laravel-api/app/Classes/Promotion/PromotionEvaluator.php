<?php

declare(strict_types=1);

namespace App\Classes\Promotion;

use App\Models\Promotion;
use App\Classes\Promotion\PromotionHandlerFactory;
use Illuminate\Support\Collection;

class PromotionEvaluator
{
    protected PromotionHandlerFactory $factory;

    public function __construct(PromotionHandlerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Evaluate and apply promotions to a given context.
     *
     * @param Collection $promotions
     * @param array $context ['subtotal' => float, 'items' => array, 'customer_id' => int|null]
     * @return array Updated context with discounts applied
     */
    public function evaluate(Collection $promotions, array $context): array
    {
        $context['total_discount'] = 0;
        $context['applied_promotions'] = [];
        $context['discounts'] = [];

        // Sort promotions by priority (highest first)
        $sortedPromotions = $promotions->sortByDesc('priority');

        foreach ($sortedPromotions as $promotion) {
            if ($this->canApplyPromotion($promotion, $context)) {
                $this->applyPromotion($promotion, $context);

                // If promotion cannot be combined, stop processing further promotions
                if (!$promotion->can_combine) {
                    break;
                }
            }
        }

        $context['final_total'] = max(0, $context['subtotal'] - $context['total_discount']);

        return $context;
    }

    /**
     * Check if a promotion's rules and targets are satisfied.
     *
     * @param Promotion $promotion
     * @param array $context
     * @return bool
     */
    protected function canApplyPromotion(Promotion $promotion, array $context): bool
    {
        // 1. Evaluate targets (If targets exist, at least one target must match)
        if (!$this->isTargetSatisfied($promotion, $context)) {
            return false;
        }

        // 2. Evaluate rules
        $rules = $promotion->promotion_rules;
        if ($rules->isEmpty()) {
            return true;
        }

        // Group rules by rule_group
        $groupedRules = $rules->groupBy('rule_group');

        foreach ($groupedRules as $groupId => $group) {
            $groupSatisfied = true;
            $logicOperator = $group->first()->logic_operator;

            foreach ($group as $rule) {
                $handler = $this->factory->getRuleHandler($rule->rule_type);
                $isSatisfied = $handler->check($rule, $context);

                if ($logicOperator === 'AND') {
                    if (!$isSatisfied) {
                        $groupSatisfied = false;
                        break;
                    }
                } else { // OR
                    if ($isSatisfied) {
                        $groupSatisfied = true;
                        break;
                    }
                    $groupSatisfied = false;
                }
            }

            // All rule groups must be satisfied (AND between groups)
            if (!$groupSatisfied) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the promotion targets are satisfied by the context.
     *
     * @param Promotion $promotion
     * @param array $context
     * @return bool
     */
    protected function isTargetSatisfied(Promotion $promotion, array $context): bool
    {
        $targets = $promotion->promotion_targets;
        if ($targets->isEmpty()) {
            return true; // No targets means applies to everything (depending on type)
        }

        $items = $context['items'] ?? [];
        $hasInclusions = $targets->where('is_included', true)->isNotEmpty();

        // If there are specific inclusions, the context must match at least one
        if ($hasInclusions) {
            $matched = false;
            foreach ($targets->where('is_included', true) as $target) {
                if ($this->matchTarget($target, $items)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                return false;
            }
        }

        // Check exclusions (If any item matches an exclusion, we might want to exclude it from discount or the whole promotion)
        // For simplicity, if any target is excluded and matches, we return false for the whole promotion application (assuming order level)
        foreach ($targets->where('is_included', false) as $target) {
            if ($this->matchTarget($target, $items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Match a single target against cart items.
     *
     * @param \App\Models\PromotionTarget $target
     * @param array $items
     * @return bool
     */
    protected function matchTarget($target, array $items): bool
    {
        foreach ($items as $item) {
            $itemId = $item['id'] ?? null;
            $itemType = $item['type'] ?? 'product'; // assume product by default

            if ($target->target_type === 'product' && $itemType === 'product' && $target->target_id == $itemId) {
                return true;
            }
            if ($target->target_type === 'category' && ($item['category_id'] ?? null) == $target->target_id) {
                return true;
            }
            if ($target->target_type === 'brand' && ($item['brand_id'] ?? null) == $target->target_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Apply a promotion's actions to the context.
     *
     * @param Promotion $promotion
     * @param array $context
     * @return void
     */
    protected function applyPromotion(Promotion $promotion, array &$context): void
    {
        $actions = $promotion->promotion_actions;

        foreach ($actions as $action) {
            $handler = $this->factory->getActionHandler($action->action_type);
            $handler->apply($action, $context);
        }

        $context['applied_promotions'][] = [
            'id' => $promotion->id,
            'code' => $promotion->code,
            'name' => $promotion->name,
            'type' => $promotion->type,
        ];
    }
}
