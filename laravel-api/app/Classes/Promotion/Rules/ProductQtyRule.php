<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Rules;

use App\Models\PromotionRule;

class ProductQtyRule implements PromotionRuleHandler
{
    /**
     * Check if cart items meet the quantity requirement for specific products.
     *
     * @param PromotionRule $rule
     * @param array $context ['items' => [['id' => int, 'qty' => int]]]
     * @return bool
     */
    public function check(PromotionRule $rule, array $context): bool
    {
        $items = $context['items'] ?? [];
        $requiredProductIds = $rule->value['product_ids'] ?? [];
        $requiredQty = (int) ($rule->value['qty'] ?? 0);

        $totalQty = 0;
        foreach ($items as $item) {
            if (empty($requiredProductIds) || in_array($item['id'], $requiredProductIds)) {
                $totalQty += $item['qty'];
            }
        }

        return match ($rule->operator) {
            '>=' => $totalQty >= $requiredQty,
            '>' => $totalQty > $requiredQty,
            '=' => $totalQty == $requiredQty,
            default => false,
        };
    }
}
