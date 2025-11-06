<?php

namespace App\Http\Requests\Cart\Cart;

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
            'customer_id' => 'nullable|exists:users,id',
            'guest_id'    => 'nullable|string|max:64|unique:carts,guest_id',
            'merged_id'   => 'nullable|exists:carts,id',
            'coupon_code' => 'nullable|string|max:50',
            'meta'        => 'nullable|array',
            'publish'     => 'required|integer|in:0,1,2',
        ];
    }

    public function prepareForValidation()
    {
        $user = auth(Common::API)->user();
        if ($user) {
            $this->merge([
                'customer_id' => $user->id,
            ]);
        }
    }

    public function messages()
    {
        return [];
    }
}
