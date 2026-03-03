<?php

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->boolean('simple', false)) {
            return [
                'id' => $this->id,
                'code' => $this->code,
                'name' => $this->name,
                'slug' => $this->slug,
                'image' => $this->image,
                'type' => $this->type,
                'priority' => $this->priority,
                'promotionCatalogueId' => $this->promotion_catalogue_id,
                'publish' => (int) $this->publish,
                'canCombine' => (bool) $this->can_combine,
                'combinableWith' => $this->combinable_with,
                'usageLimit' => (int) $this->usage_limit,
                'usagePerCustomer' => (int) $this->usage_per_customer,
                'usedCount' => (int) $this->used_count,
                'startDate' => $this->start_date,
                'endDate' => $this->end_date,
                'createdAt' => $this->created_at ?? null,
                'updatedAt' => $this->updated_at ?? null,
            ];
        }
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,
            'type' => $this->type,
            'priority' => $this->priority,
            'promotionCatalogueId' => $this->promotion_catalogue_id,
            'promotionCatalogue' => $this->whenLoaded('promotion_catalogue', function () {
                return $this->promotion_catalogue;
            }),
            'canCombine' => (bool)$this->can_combine,
            'combinableWith' => $this->combinable_with,
            'usageLimit' => $this->usage_limit,
            'usagePerCustomer' => $this->usage_per_customer,
            'usedCount' => $this->used_count,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'publish' => (int)$this->publish,
            'isActive' => (bool)($this->is_active ?? false),
            'remainingUsage' => $this->remaining_usage ?? null,
            'rules' => $this->whenLoaded('promotion_rules', function () {
                return $this->promotion_rules->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'ruleGroup' => $r->rule_group,
                        'logicOperator' => $r->logic_operator,
                        'ruleType' => $r->rule_type,
                        'operator' => $r->operator,
                        'value' => $r->value,
                        'parsedValue' => $r->parsed_value,
                        'description' => $r->description,
                    ];
                });
            }),
            'actions' => $this->whenLoaded('promotion_actions', function () {
                return $this->promotion_actions->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'executionOrder' => $a->execution_order,
                        'actionType' => $a->action_type,
                        'actionValue' => $a->action_value,
                        'maxDiscount' => $a->max_discount,
                        'minDiscount' => $a->min_discount,
                        'applyTo' => $a->apply_to,
                        'config' => $a->config,
                    ];
                });
            }),
            'targets' => $this->whenLoaded('promotion_targets', function () {
                return $this->promotion_targets->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'targetType' => $t->target_type,
                        'targetId' => $t->target_id,
                        'isIncluded' => (bool)$t->is_included,
                        'metadata' => $t->metadata,
                    ];
                });
            }),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
