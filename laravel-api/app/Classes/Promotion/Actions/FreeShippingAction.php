<?php

namespace App\Classes\Promotion\Actions;

use App\Models\PromotionAction;

class FreeShippingAction implements PromotionActionHandler
{
    /**
     * Calculate discount amount (0 for free shipping as it's separate).
     */
    public function calculateDiscount(PromotionAction $action, array $context): float
    {
        return 0.0;
    }

    /**
     * Apply free shipping action.
     * 
     * @param PromotionAction $action
     * @param array &$context
     * @return array
     */
    public function apply(PromotionAction $action, array &$context): array
    {
        // For free shipping, we don't necessarily reduce the cart subtotal here
        // usually it's handled at the shipping calculation level.
        // However, we record it so the shipping service can pick it up.

        return [
            'type' => 'free_shipping',
            'value' => 0,
            'amount' => 0, // Amount depends on shipping fee calculation
            'action_id' => $action->id,
            'message' => 'Miễn phí vận chuyển'
        ];
    }
}
