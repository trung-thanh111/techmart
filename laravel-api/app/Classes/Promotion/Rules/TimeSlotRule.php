<?php

namespace App\Classes\Promotion\Rules;

use App\Models\PromotionRule;
use Carbon\Carbon;

class TimeSlotRule implements PromotionRuleHandler
{
    /**
     * Check if current time falls within the required time slot.
     * 
     * @param PromotionRule $rule
     * @param array $context
     * @return bool
     */
    public function check(PromotionRule $rule, array $context): bool
    {
        $value = $rule->parsed_value;
        $from = $value['from'] ?? null;
        $to = $value['to'] ?? null;

        if (!$from || !$to) {
            return true;
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        return $currentTime >= $from && $currentTime <= $to;
    }
}
