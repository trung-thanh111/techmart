<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Promotion;
use App\Models\PromotionTarget;
use App\Models\Product;

class PromotionSuperSaleTest extends TestCase
{
    public function test_super_sale_promotion_exists_and_assigned_to_products(): void
    {
        $superSale = Promotion::where('code', 'SUPERSALE')->first();

        $this->assertNotNull($superSale, 'Promotion SUPERSALE should exist');
        $this->assertEquals('product', $superSale->type);
        $this->assertEquals(2, $superSale->publish);

        // Verify actions
        $this->assertTrue($superSale->promotion_actions()->count() > 0);
        $action = $superSale->promotion_actions()->first();
        $this->assertEquals('percent', $action->action_type);
        $this->assertEquals(20, $action->action_value);

        // Verify targets are assigned
        $targetsCount = PromotionTarget::where('promotion_id', $superSale->id)
            ->where('target_type', 'product')
            ->count();

        $this->assertGreaterThan(0, $targetsCount, 'Super sale should be assigned to at least one product');
    }

    public function test_public_promotions_api_returns_super_sale(): void
    {
        $response = $this->getJson('/api/v1/promotions');

        $response->assertStatus(200);
        $data = $response->json('data.data');

        $this->assertIsArray($data);
        $found = collect($data)->firstWhere('code', 'SUPERSALE');
        $this->assertNotNull($found, 'Public promotions API should return SUPERSALE');
    }
}
