<?php

namespace App\Http\Requests\Product\Wishlist;

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
     * // Rule này sẽ bỏ qua validation slug theo id trong bảng user_catalogues
     * Rule::unique('user_catalogues')->ignore($this->route('user_catalogue'))
     *
     * // prepareForValidation
     * $this->merge([
     *      'slug' => Str::slug($this->name),
     *  ]);
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        return [
            "user_id" => "{$sometime}|required|integer|min:0",
            "product_variant_id" => "{$sometime}|required|integer|min:0",
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
