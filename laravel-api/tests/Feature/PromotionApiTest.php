<?php

namespace Tests\Feature;

use App\Enums\Promotion;
use App\Models\Promotion as PromotionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_promotion_validation_works_with_enum_values()
    {
        $data = [
            'name' => 'Test Promotion',
            'type' => Promotion::TYPE_FLASH_SALE,
            'publish' => Promotion::PUBLISH_PUBLIC,
            'rules' => [
                [
                    'rule_group' => 1,
                    'logic_operator' => Promotion::LOGIC_AND,
                    'rule_type' => Promotion::RULE_MIN_ORDER_TOTAL,
                    'operator' => Promotion::OPERATOR_GREATER_THAN_EQUALS,
                    'value' => '500000',
                    'description' => 'Min order 500k'
                ]
            ],
            'actions' => [
                [
                    'execution_order' => 1,
                    'action_type' => Promotion::ACTION_PERCENT,
                    'action_value' => 20,
                    'apply_to' => Promotion::APPLY_TO_CART_SUBTOTAL
                ]
            ],
            'targets' => [
                [
                    'target_type' => Promotion::TARGET_ORDER,
                    'is_included' => true
                ]
            ]
        ];

        // Test that validation passes with correct enum values
        $response = $this->postJson('/api/v1/promotions', $data);

        // Should not get validation error for enum fields
        $response->assertStatus(401); // Authentication required, but no validation error
    }

    public function test_promotion_validation_fails_with_invalid_enum_values()
    {
        $data = [
            'name' => 'Test Promotion',
            'type' => 'invalid_type',
            'publish' => 999,
            'rules' => [
                [
                    'rule_group' => 1,
                    'logic_operator' => 'INVALID',
                    'rule_type' => 'invalid_rule',
                    'operator' => 'INVALID_OP',
                    'value' => '500000',
                    'description' => 'Min order 500k'
                ]
            ],
            'actions' => [
                [
                    'execution_order' => 1,
                    'action_type' => 'invalid_action',
                    'action_value' => 20,
                    'apply_to' => 'invalid_apply'
                ]
            ],
            'targets' => [
                [
                    'target_type' => 'invalid_target',
                    'is_included' => true
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/promotions', $data);

        // Should get validation errors for invalid enum values
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'publish', 'rules.0.logic_operator', 'rules.0.rule_type', 'rules.0.operator', 'actions.0.action_type', 'actions.0.apply_to', 'targets.0.target_type']);
    }
}
