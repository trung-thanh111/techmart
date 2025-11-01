<?php

namespace App\Http\Requests\Product\Wishlist;

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
            "user_id" => "required|integer|min:0",
            "product_variant_id" => "required|integer|min:0",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
