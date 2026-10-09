<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Models\PromotionAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PromotionApplyDiscountEndpointTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $promo = Promotion::firstOrCreate(
            ['code' => 'TESTPROMO10'],
            [
                'name' => 'Test Promo 10%',
                'slug' => 'test-promo-10',
                'type' => 'order',
                'priority' => 10,
                'can_combine' => false,
                'usage_limit' => 100,
                'publish' => 2,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonth(),
            ]
        );

        PromotionRule::firstOrCreate(
            [
                'promotion_id' => $promo->id,
                'rule_type' => 'min_order_total',
            ],
            [
                'rule_group' => 0,
                'logic_operator' => 'AND',
                'operator' => '>=',
                'value' => ['amount' => 200000],
            ]
        );

        PromotionAction::firstOrCreate(
            [
                'promotion_id' => $promo->id,
                'action_type' => 'percent',
            ],
            [
                'execution_order' => 0,
                'action_value' => 10,
                'max_discount' => 50000,
                'apply_to' => 'order',
            ]
        );
    }

    public function test_apply_discount_successfully_with_valid_code()
    {
        $payload = [
            'code' => 'TESTPROMO10',
            'subtotal' => 300000,
            'items' => [
                ['id' => 1, 'qty' => 1]
            ]
        ];

        $response = $this->postJson('/api/v1/promotion/apply_discount', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'code' => 200,
                'data' => [
                    'code' => 'TESTPROMO10',
                    'subtotal' => 300000,
                    'total_discount' => 30000,
                    'final_total' => 270000,
                    'success' => true,
                ]
            ]);
    }

    public function test_apply_discount_fails_when_subtotal_below_minimum()
    {
        $payload = [
            'code' => 'TESTPROMO10',
            'subtotal' => 100000, // Đơn 100k < 200k yêu cầu
            'items' => [
                ['id' => 1, 'qty' => 1]
            ]
        ];

        $response = $this->postJson('/api/v1/promotion/apply_discount', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'code' => 422,
                'message' => 'Đơn hàng chưa thỏa mãn điều kiện áp dụng mã giảm giá này.',
            ]);
    }

    public function test_apply_discount_fails_when_code_not_exists()
    {
        $payload = [
            'code' => 'INVALID_CODE_999',
            'subtotal' => 500000,
            'items' => [
                ['id' => 1, 'qty' => 1]
            ]
        ];

        $response = $this->postJson('/api/v1/promotion/apply_discount', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'code' => 422,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn/lượt dùng.',
            ]);
    }

    public function test_apply_discount_validation_errors()
    {
        // Thiếu subtotal và items
        $response = $this->postJson('/api/v1/promotion/apply_discount', [
            'code' => 'TESTPROMO10',
        ]);

        $response->assertStatus(422);
    }
}
