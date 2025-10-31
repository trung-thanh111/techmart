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
            "description" => "{$sometime}|nullable|string",
            "content" => "{$sometime}|nullable|string",
            "price_origin" => "{$sometime}|required|numeric|min:0",
            "album" => "{$sometime}|nullable|sometimes|array",
            "sort_order" => "{$sometime}|nullable|integer|min:0",
            "product_catalogue_id" => "{$sometime}|nullable|integer|min:0",
            "brand_id" => "{$sometime}|nullable|integer|min:0",
            "faq_id" => "{$sometime}|nullable|integer|min:0",
            "tag_id" => "{$sometime}|nullable|integer|min:0",
            "is_hot" => "{$sometime}|nullable|boolean",
            "like_count" => "{$sometime}|nullable|integer|min:0",
            "sold" => "{$sometime}|nullable|integer|min:0",
            "warranty" => "{$sometime}|nullable|integer|min:0",
            "publish" => "{$sometime}|nullable|integer|in:0,1,2",
            "user_id" => "{$sometime}|nullable|integer|exists:users,id",
            "meta_title" => "{$sometime}|nullable|string|max:255",
            "meta_keyword" => "{$sometime}|nullable|string|max:255",
            "meta_description" => "{$sometime}|nullable|string|max:1000",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique('products')->ignore($this->route('product')),
            ],
            "sku" => [
                "{$sometime}",
                "required",
                "string",
                "max:100",
                Rule::unique('products')->ignore($this->route('product')),
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'like_count' => $this->like_count ?? 0,
            'updated_at' => now(),
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
