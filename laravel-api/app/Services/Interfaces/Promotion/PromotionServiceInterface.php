<?php

namespace App\Services\Interfaces\Promotion;

interface PromotionServiceInterface
{
    /**
     * Calculate discount for a given cart context.
     *
     * @param array $context
     * @return array
     */
    public function calculateDiscount(array $context): array;
}
