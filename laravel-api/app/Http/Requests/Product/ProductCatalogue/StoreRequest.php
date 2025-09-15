<?php

namespace App\Http\Requests\Product\ProductCatalogue;

use App\Enums\Common;
use Illuminate\Support\Str;
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
            'parent_id' => 'nullable|integer',
            'lft' => 'nullable|integer',
            'rgt' => 'nullable|integer',
            'level' => 'nullable|integer|min:0',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_catalogues,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'publish' => 'nullable|integer|in:1,2',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'index' => 'nullable|integer|in:1,2',
            'scheme' => 'nullable|string',
            'iframe' => 'nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
