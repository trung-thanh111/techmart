<?php

namespace App\Http\Requests\Promotion\Promotion;

use App\Enums\Common;
use App\Http\Requests\BaseRequest;

class CalculateDiscountRequest extends BaseRequest
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
            'subtotal' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|integer',
            'customer_id' => 'nullable|integer',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'customer_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
