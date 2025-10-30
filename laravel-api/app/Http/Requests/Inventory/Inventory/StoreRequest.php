<?php

namespace App\Http\Requests\Inventory\Inventory;

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
            "product_variant_id" => "required|exists:product_variants,id",
            "agency_id"          => "required|exists:agencies,id",
            "stock"              => "nullable|integer|min:0",
            "price_override"     => "nullable|numeric|min:0",
            "publish"            => "required|in:0,1,2",
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
