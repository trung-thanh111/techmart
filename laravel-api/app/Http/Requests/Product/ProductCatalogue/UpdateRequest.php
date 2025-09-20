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
        return [
            "parent_id" => "{$sometime}|nullable|integer|exists:product_catalogues,id",
            "lft" => "{$sometime}|nullable|integer",
            "rgt" => "{$sometime}|nullable|integer",
            "level" => "{$sometime}|nullable|integer|min:0",
            "name" => "{$sometime}|required|string|max:255",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique("product_catalogues", "slug")->ignore($this->route('product_catalogue'))
            ],
            "description" => "{$sometime}|nullable|string",
            "image" => "{$sometime}|nullable|string|max:255",
            "icon" => "{$sometime}|nullable|string|max:255",
            "order" => "{$sometime}|nullable|integer",
            "publish" => "{$sometime}|nullable|integer|in:1,2",
            "user_id" => "{$sometime}|nullable|integer|exists:users,id",
            "meta_title" => "{$sometime}|nullable|string|max:255",
            "meta_keyword" => "{$sometime}|nullable|string|max:255",
            "meta_description" => "{$sometime}|nullable|string|max:500",
            "index" => "{$sometime}|nullable|integer|in:1,2",
            "scheme" => "{$sometime}|nullable|string",
            "iframe" => "{$sometime}|nullable|string",
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
