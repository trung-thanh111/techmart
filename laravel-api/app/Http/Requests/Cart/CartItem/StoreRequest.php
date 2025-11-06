<?php

namespace App\Http\Requests\Cart\CartItem;

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
            'cart_id'           => 'required|exists:carts,id',
            'purchasable_type'  => 'required|string|max:255',
            'purchasable_id'    => 'required|integer',
            'quantity'          => 'required|integer|min:1',
            'price'             => 'required|numeric|min:0',
            'meta'              => 'nullable|array',
            'publish'           => 'required|integer|in:0,1,2',
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
