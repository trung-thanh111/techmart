<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Rules;

use App\Models\PromotionRule;

class MinOrderTotalRule implements PromotionRuleHandler
{
    /**
     * Check if order subtotal meets the minimum requirement.
     *
     * @param PromotionRule $rule
     * @param array $context ['subtotal' => float]
     * @return bool
     */
    public function check(PromotionRule $rule, array $context): bool
    {
        $subtotal = $context['subtotal'] ?? 0;
        $val = $rule->value;
        if (is_array($val)) {
            $requiredAmount = (float) ($val['amount'] ?? reset($val) ?? 0);
        } else {
            $requiredAmount = (float) ($val ?? 0);
        }

        return match ($rule->operator) {
            '>=' => $subtotal >= $requiredAmount,
            '>' => $subtotal > $requiredAmount,
            '=' => $subtotal == $requiredAmount,
            default => false,
        };
    }
}
