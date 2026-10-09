<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Models\PromotionAction;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            [
                'code' => 'TECHMART10',
                'name' => 'Giảm 10% đơn từ 200k',
                'slug' => 'giam-10-don-tu-200k',
                'description' => 'Giảm 10% tối đa 100.000đ cho đơn hàng từ 200.000đ',
                'type' => 'order',
                'priority' => 10,
                'can_combine' => false,
                'usage_limit' => 1000,
                'usage_per_customer' => 5,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(6),
                'publish' => 2,
                'rules' => [
                    [
                        'rule_group' => 0,
                        'logic_operator' => 'AND',
                        'rule_type' => 'min_order_total',
                        'operator' => '>=',
                        'value' => ['amount' => 200000],
                        'description' => 'Đơn tối thiểu 200.000đ',
                    ]
                ],
                'actions' => [
                    [
                        'execution_order' => 0,
                        'action_type' => 'percent',
                        'action_value' => 10,
                        'max_discount' => 100000,
                        'apply_to' => 'order',
                    ]
                ]
            ],
            [
                'code' => 'GIAM50K',
                'name' => 'Giảm 50.000đ đơn từ 500k',
                'slug' => 'giam-50k-don-tu-500k',
                'description' => 'Giảm 50.000đ cho đơn hàng từ 500.000đ',
                'type' => 'order',
                'priority' => 20,
                'can_combine' => false,
                'usage_limit' => 500,
                'usage_per_customer' => 2,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(6),
                'publish' => 2,
                'rules' => [
                    [
                        'rule_group' => 0,
                        'logic_operator' => 'AND',
                        'rule_type' => 'min_order_total',
                        'operator' => '>=',
                        'value' => ['amount' => 500000],
                        'description' => 'Đơn tối thiểu 500.000đ',
                    ]
                ],
                'actions' => [
                    [
                        'execution_order' => 0,
                        'action_type' => 'fixed',
                        'action_value' => 50000,
                        'max_discount' => 50000,
                        'apply_to' => 'order',
                    ]
                ]
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Freeship 30.000đ',
                'slug' => 'freeship-30k',
                'description' => 'Giảm 30.000đ tiền vận chuyển đơn từ 300.000đ',
                'type' => 'order',
                'priority' => 5,
                'can_combine' => false,
                'usage_limit' => 1000,
                'usage_per_customer' => 5,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(6),
                'publish' => 2,
                'rules' => [
                    [
                        'rule_group' => 0,
                        'logic_operator' => 'AND',
                        'rule_type' => 'min_order_total',
                        'operator' => '>=',
                        'value' => ['amount' => 300000],
                        'description' => 'Đơn tối thiểu 300.000đ',
                    ]
                ],
                'actions' => [
                    [
                        'execution_order' => 0,
                        'action_type' => 'fixed',
                        'action_value' => 30000,
                        'max_discount' => 30000,
                        'apply_to' => 'order',
                    ]
                ]
            ],
            [
                'code' => 'SUPERSALE',
                'name' => 'Siêu Sale Công Nghệ 2026',
                'slug' => 'sieu-sale-cong-nghe-2026',
                'description' => '<p>Đại tiệc Siêu Sale Công Nghệ: Giảm trực tiếp 20% cho các sản phẩm công nghệ hàng đầu.</p>',
                'type' => 'product',
                'priority' => 100,
                'can_combine' => true,
                'usage_limit' => 50000,
                'usage_per_customer' => 10,
                'used_count' => 0,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addMonths(6),
                'publish' => 2,
                'rules' => [],
                'actions' => [
                    [
                        'execution_order' => 0,
                        'action_type' => 'percent',
                        'action_value' => 20,
                        'max_discount' => 5000000,
                        'apply_to' => 'product',
                    ]
                ],
                'targets' => 'hot_products',
            ],
        ];

        foreach ($promotions as $promoData) {
            $rules = $promoData['rules'];
            $actions = $promoData['actions'];
            $targets = $promoData['targets'] ?? null;
            unset($promoData['rules'], $promoData['actions'], $promoData['targets']);

            $promotion = Promotion::firstOrCreate(
                ['code' => $promoData['code']],
                $promoData
            );

            // Gán rules
            foreach ($rules as $rule) {
                PromotionRule::firstOrCreate(
                    [
                        'promotion_id' => $promotion->id,
                        'rule_type' => $rule['rule_type'],
                        'operator' => $rule['operator'],
                    ],
                    $rule
                );
            }

            // Gán actions
            foreach ($actions as $action) {
                PromotionAction::firstOrCreate(
                    [
                        'promotion_id' => $promotion->id,
                        'action_type' => $action['action_type'],
                    ],
                    $action
                );
            }

            // Gán targets cho sản phẩm
            if ($targets === 'hot_products') {
                $hotProducts = \App\Models\Product::where('is_hot', 1)->take(10)->get();
                if ($hotProducts->isEmpty()) {
                    $hotProducts = \App\Models\Product::take(10)->get();
                }
                foreach ($hotProducts as $prod) {
                    \App\Models\PromotionTarget::firstOrCreate(
                        [
                            'promotion_id' => $promotion->id,
                            'target_type' => 'product',
                            'target_id' => $prod->id,
                        ],
                        [
                            'is_included' => true,
                            'metadata' => ['product_name' => $prod->name, 'sku' => $prod->sku],
                        ]
                    );
                }
            }
        }
    }
}
