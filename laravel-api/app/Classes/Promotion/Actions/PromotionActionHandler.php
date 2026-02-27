<?php

declare(strict_types=1);

namespace App\Classes\Promotion\Actions;

use App\Models\PromotionAction;

interface PromotionActionHandler
{
    /**
     * Calculate discount amount based on the action and context.
     *
     * @param PromotionAction $action
     * @param array $context
     * @return float
     */
    public function calculateDiscount(PromotionAction $action, array $context): float;

    /**
     * Apply the action (e.g., modify the cart/order context).
     *
     * @param PromotionAction $action
     * @param array $context
     * @return array Updated context
     */
    public function apply(PromotionAction $action, array &$context): array;
}
