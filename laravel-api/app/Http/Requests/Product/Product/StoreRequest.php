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
            "name" => "required|string|max:255",
             "slug" => "required|string|max:255|unique:products,slug",
            "description" => "nullable|string|max:255",
            "content" => "nullable|string",
            "made_in" => "nullable|string",
            "image" => "nullable|string|max:255",
            "icon" => "nullable|string|max:255",
            "album" => "nullable|string",
            "extra_album" => "nullable|string",
            "price" => "required|integer|min:0",
            "order" => "nullable|integer|min:0",
            "product_catalogue_id" => "nullable|integer|min:0",
            "brand_id" => "nullable|integer|min:0",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'published_at' => ($this->publish === 2) ? now() : '',
        ]);
    }

    public function messages(){
        return [];
    }
}
