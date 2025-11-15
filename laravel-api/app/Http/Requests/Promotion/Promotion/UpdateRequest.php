<?php

namespace App\Http\Requests\Promotion\Promotion;

use App\Enums\Common;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
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
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        $promotionId = $this->route('promotion');

        return [
            "name" => "{$sometime}|required|string|max:255",
            "couponCode" => [
                "{$sometime}",
                "nullable",
                "string",
                "max:100",
                Rule::unique('promotions', 'coupon_code')->ignore($promotionId),
            ],
            "description" => "{$sometime}|nullable|string",
            "promotionCatalogueId" => "{$sometime}|nullable|integer|exists:promotion_catalogues,id",
            "discountValue" => "{$sometime}|required|numeric|min:0",
            "discountType" => "{$sometime}|required|string|in:percent,amount",
            "maxDiscountValue" => "{$sometime}|nullable|numeric|min:0",
            "startDate" => "{$sometime}|nullable|date",
            "endDate" => "{$sometime}|nullable|date|after_or_equal:startDate",
            "priority" => "{$sometime}|nullable|integer|min:0",
            "canCombine" => "{$sometime}|nullable|boolean",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
            "promotionTargets" => "{$sometime}|nullable|array",
            "promotionTargets.*.productId" => "nullable|integer|exists:products,id|required_without_all:promotionTargets.*.productVariantId,promotionTargets.*.productCatalogueId",
            "promotionTargets.*.productVariantId" => "nullable|integer|exists:product_variants,id|required_without_all:promotionTargets.*.productId,promotionTargets.*.productCatalogueId",
            "promotionTargets.*.productCatalogueId" => "nullable|integer|exists:product_catalogues,id|required_without_all:promotionTargets.*.productId,promotionTargets.*.productVariantId",
            "promotionTargets.*.isGift" => "nullable|boolean",
            "promotionTargets.*.minQuantity" => "nullable|integer|min:1",
            "promotionTargets.*.maxQuantity" => "nullable|integer|min:1|gt:promotionTargets.*.minQuantity",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'coupon_code' => $this->couponCode ?? null,
            'promotion_catalogue_id' => $this->promotionCatalogueId ?? 0,
            'discount_value' => $this->discountValue ?? 0,
            'discount_type' => $this->discountType ?? 'amount',
            'max_discount_value' => $this->maxDiscountValue ?? null,
            'start_date' => $this->startDate ?? null,
            'end_date' => $this->endDate ?? null,
            'priority' => $this->priority ?? 0,
            'can_combine' => $this->canCombine ?? false,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
