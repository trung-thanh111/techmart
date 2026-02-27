<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Actions;

use App\Models\PromotionAction;

class FixedAmountAction implements PromotionActionHandler
{
    /**
     * @param PromotionAction $action
     * @param array $context ['subtotal' => float]
     * @return float
     */
    public function calculateDiscount(PromotionAction $action, array $context): float
    {
        $discount = (float) $action->action_value;
        $subtotal = $context['subtotal'] ?? 0;

        // Discount cannot exceed subtotal
        return min($discount, $subtotal);
    }

    public function apply(PromotionAction $action, array &$context): array
    {
        $discount = $this->calculateDiscount($action, $context);
        $context['discounts'][] = [
            'type' => 'fixed',
            'value' => $action->action_value,
            'amount' => $discount,
            'action_id' => $action->id,
        ];
        $context['total_discount'] = ($context['total_discount'] ?? 0) + $discount;

        return $context;
    }
}
