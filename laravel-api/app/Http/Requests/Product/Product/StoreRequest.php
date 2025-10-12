<?php
namespace App\Http\Requests\Product\Product;

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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'required|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'price_origin' => 'required|numeric|min:0',
            "album" => "nullable|sometimes|array",
            'sort_order' => 'nullable|integer|min:0',
            'product_catalogue_id' => 'nullable|integer|min:0',
            'brand_id' => 'nullable|integer|min:0',
            'faq_id' => 'nullable|integer|min:0',
            'tag_id' => 'nullable|integer|min:0',
            'is_hot' => 'nullable|boolean',
            'like_count' => 'nullable|integer|min:0',
            'publish' => 'nullable|integer|in:0,1,2',
            'user_id' => 'nullable|integer|exists:users,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'like_count' => $this->like_count ?? 0,
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages(){
        return [];
    }
}
