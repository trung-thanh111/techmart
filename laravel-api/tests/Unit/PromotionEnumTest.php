<?php

namespace Tests\Unit;

use App\Enums\Promotion;
use PHPUnit\Framework\TestCase;

class PromotionEnumTest extends TestCase
{
    public function test_available_types_contains_flash_sale()
    {
        $this->assertContains(Promotion::TYPE_FLASH_SALE, Promotion::AVAILABLE_TYPES);
    }

    public function test_available_action_types_contains_percent()
    {
        $this->assertContains(Promotion::ACTION_PERCENT, Promotion::AVAILABLE_ACTION_TYPES);
    }

    public function test_available_rule_types_contains_min_order_total()
    {
        $this->assertContains(Promotion::RULE_MIN_ORDER_TOTAL, Promotion::AVAILABLE_RULE_TYPES);
    }

    public function test_publish_status_constants()
    {
        $this->assertEquals(0, Promotion::PUBLISH_DRAFT);
        $this->assertEquals(1, Promotion::PUBLISH_PRIVATE);
        $this->assertEquals(2, Promotion::PUBLISH_PUBLIC);
    }
}
