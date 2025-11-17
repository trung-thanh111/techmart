<?php

namespace App\Http\Requests\Promotion\Promotion;

use App\Enums\Common;
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
        return [
            "name" => "required|string|max:255",
            "couponCode" => "nullable|string|max:100|unique:promotions,coupon_code",
            "description" => "nullable|string",
            "promotionCatalogueId" => "nullable|integer|exists:promotion_catalogues,id",
            "discountValue" => "required|numeric|min:0",
            "discountType" => "required|string|in:percent,amount",
            "maxDiscountValue" => "nullable|numeric|min:0",
            "startDate" => "nullable|date",
            "endDate" => "nullable|date|after_or_equal:startDate",
            "priority" => "nullable|integer|min:0",
            "canCombine" => "nullable|boolean",
            "publish" => "required|integer|in:0,1,2",
            "promotionTargets" => "nullable|array",
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
            'used_count' => 0,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
