<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Rules;

use App\Models\PromotionRule;

interface PromotionRuleHandler
{
    /**
     * Check if the rule is satisfied by the given context (cart, order, data).
     *
     * @param PromotionRule $rule
     * @param array $context
     * @return bool
     */
    public function check(PromotionRule $rule, array $context): bool;
}
