<?php

namespace App\Http\Requests\Promotion\Promotion;

use App\Enums\Common;
use App\Enums\Promotion;
use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promotions,code',
            'slug' => 'required|string|max:255|unique:promotions,slug',
            'description' => 'nullable|string',
            "image" => "nullable|sometimes|mimes:{$mimes}|max:{$max_size}",
            'type' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_TYPES),
            'priority' => 'nullable|integer|min:0',
            'promotion_catalogue_id' => 'nullable|integer|exists:promotion_catalogues,id',
            'can_combine' => 'nullable|boolean',
            'combinable_with' => 'nullable|array',
            'combinable_with.*' => 'integer|exists:promotions,id',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date|before:end_date',
            'end_date' => 'nullable|date|after:start_date',
            'publish' => 'required|integer|in:' . implode(',', Promotion::AVAILABLE_PUBLISH_STATUS),

            // nested relations
            'rules' => 'nullable|array',
            'rules.*.rule_group' => 'required|integer|min:0',
            'rules.*.logic_operator' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_LOGIC_OPERATORS),
            'rules.*.rule_type' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_RULE_TYPES),
            'rules.*.operator' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_RULE_OPERATORS),
            'rules.*.value' => 'required',
            'rules.*.description' => 'nullable|string',

            'actions' => 'nullable|array',
            'actions.*.execution_order' => 'required|integer|min:0',
            'actions.*.action_type' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_ACTION_TYPES),
            'actions.*.action_value' => 'nullable|numeric|min:0',
            'actions.*.max_discount' => 'nullable|numeric|min:0',
            'actions.*.min_discount' => 'nullable|numeric|min:0',
            'actions.*.apply_to' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_APPLY_TO),
            'actions.*.config' => 'nullable|array',

            'targets' => 'nullable|array',
            'targets.*.target_type' => 'required|string|in:' . implode(',', Promotion::AVAILABLE_TARGET_TYPES),
            'targets.*.target_id' => 'nullable|integer|min:0',
            'targets.*.is_included' => 'required|boolean',
            'targets.*.metadata' => 'nullable|array',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
