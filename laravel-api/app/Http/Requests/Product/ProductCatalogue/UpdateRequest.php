<?php

namespace App\Http\Requests\Product\ProductCatalogue;

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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "name" => "{$sometime}|required|string|max:255",
            "parent_id" => "{$sometime}|nullable|integer|exists:product_catalogues,id",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique("product_catalogues", "slug")->ignore($this->route('product_catalogue'))
            ],
            "description" => "{$sometime}|nullable|string",
            "image" => "sometimes|mimes:{$mimes}|max:{$max_size}",
            "order" => "{$sometime}|nullable|integer",
            "is_hot" => "nullable|boolean|{$sometime}",
            "publish" => "{$sometime}|nullable|integer|in:1,2",
            "meta_title" => "{$sometime}|nullable|string|max:255",
            "meta_keyword" => "{$sometime}|nullable|string|max:255",
            "meta_description" => "{$sometime}|nullable|string|max:500",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'updated_at' => now(),
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
