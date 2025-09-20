<?php

namespace App\Http\Requests\Product\Product;

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
            "name" => "{$sometime}|required|string|max:255",
            "slug" => "{$sometime}|nullable|string|max:255|unique:products,slug",
            "description" => "{$sometime}|nullable|string|max:255",
            "content" => "{$sometime}|nullable|string",
            "made_in" => "{$sometime}|nullable|string",
            "image" => "{$sometime}|nullable|string|max:255",
            "icon" => "{$sometime}|nullable|string|max:255",
            "album" => "{$sometime}|nullable|string",
            "extra_album" => "{$sometime}|nullable|string",
            "price" => "{$sometime}|required|integer|min:0",
            "order" => "{$sometime}|nullable|integer|min:0",
            "product_catalogue_id" => "{$sometime}|nullable|integer|min:0",
            "brand_id" => "{$sometime}|nullable|integer|min:0",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique('products')->ignore($this->route('product')),
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'updated_at' => now(),
           ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
