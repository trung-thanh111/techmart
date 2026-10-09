<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PromotionRule;
use App\Models\PromotionAction;
use App\Classes\Promotion\Rules\MinOrderTotalRule;
use App\Classes\Promotion\Actions\PercentDiscountAction;
use App\Classes\Promotion\Actions\FixedAmountAction;

class PromotionDiscountTest extends TestCase
{
    public function test_min_order_total_rule_satisfied_when_subtotal_is_greater_or_equal()
    {
        $rule = new PromotionRule([
            'operator' => '>=',
            'value' => ['amount' => 200000],
        ]);

        $handler = new MinOrderTotalRule();

        // Subtotal = 250k >= 200k -> true
        $this->assertTrue($handler->check($rule, ['subtotal' => 250000]));

        // Subtotal = 200k >= 200k -> true
        $this->assertTrue($handler->check($rule, ['subtotal' => 200000]));

        // Subtotal = 150k < 200k -> false
        $this->assertFalse($handler->check($rule, ['subtotal' => 150000]));
    }

    public function test_percent_discount_action_calculates_correct_amount_with_cap()
    {
        $action = new PromotionAction([
            'action_type' => 'percent',
            'action_value' => 10, // 10%
            'max_discount' => 50000, // Trần 50k
        ]);

        $handler = new PercentDiscountAction();

        // 10% của 300k = 30k (< 50k max)
        $discount1 = $handler->calculateDiscount($action, ['subtotal' => 300000]);
        $this->assertEquals(30000, $discount1);

        // 10% của 800k = 80k (> 50k max) -> chỉ giảm 50k
        $discount2 = $handler->calculateDiscount($action, ['subtotal' => 800000]);
        $this->assertEquals(50000, $discount2);
    }

    public function test_fixed_amount_action_cannot_exceed_subtotal()
    {
        $action = new PromotionAction([
            'action_type' => 'fixed',
            'action_value' => 50000, // Giảm 50k
        ]);

        $handler = new FixedAmountAction();

        // Đơn 200k -> giảm 50k
        $discount1 = $handler->calculateDiscount($action, ['subtotal' => 200000]);
        $this->assertEquals(50000, $discount1);

        // Đơn 30k -> giảm tối đa bằng subtotal là 30k
        $discount2 = $handler->calculateDiscount($action, ['subtotal' => 30000]);
        $this->assertEquals(30000, $discount2);
    }
}
