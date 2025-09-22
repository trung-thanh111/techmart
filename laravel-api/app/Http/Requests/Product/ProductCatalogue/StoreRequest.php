<?php

namespace App\Http\Requests\Product\ProductCatalogue;

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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            'name' => 'required|string|max:255',
            "parent_id" => "nullable|integer|min:0",
            'slug' => 'nullable|string|max:255|unique:product_catalogues,slug',
            'description' => 'nullable|string',
            "image" => "sometimes|mimes:{$mimes}|max:{$max_size}",
            'order' => 'nullable|integer',
            'publish' => 'nullable|integer|in:1,2',
            "is_hot" => "nullable|boolean",
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
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
