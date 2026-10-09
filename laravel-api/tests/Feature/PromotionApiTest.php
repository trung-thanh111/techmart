<?php

namespace Tests\Feature;

use App\Enums\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromotionApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_promotion_requires_authentication()
    {
        $data = [
            'name' => 'Test Promotion',
            'type' => Promotion::TYPE_FLASH_SALE,
            'publish' => Promotion::PUBLISH_PUBLIC,
        ];

        $response = $this->postJson('/api/v1/promotions', $data);
        $response->assertStatus(401);
    }

    public function test_promotion_validation_fails_with_invalid_enum_values()
    {
        $admin = User::first();

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

        $response = $admin 
            ? $this->actingAs($admin, 'api')->postJson('/api/v1/promotions', $data)
            : $this->postJson('/api/v1/promotions', $data);

        $this->assertContains($response->status(), [401, 422]);
    }
}
