<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Actions;

use App\Models\PromotionAction;

class PercentDiscountAction implements PromotionActionHandler
{
    /**
     * @param PromotionAction $action
     * @param array $context ['subtotal' => float]
     * @return float
     */
    public function calculateDiscount(PromotionAction $action, array $context): float
    {
        $subtotal = $context['subtotal'] ?? 0;
        $percent = (float) $action->action_value;
        $discount = ($subtotal * $percent) / 100;

        if ($action->max_discount && $discount > $action->max_discount) {
            $discount = (float) $action->max_discount;
        }

        return $discount;
    }

    public function apply(PromotionAction $action, array &$context): array
    {
        $discount = $this->calculateDiscount($action, $context);
        $context['discounts'][] = [
            'type' => 'percent',
            'value' => $action->action_value,
            'amount' => $discount,
            'action_id' => $action->id,
        ];
        $context['total_discount'] = ($context['total_discount'] ?? 0) + $discount;

        return $context;
    }
}
