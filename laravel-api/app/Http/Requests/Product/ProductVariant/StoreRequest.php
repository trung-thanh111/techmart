<?php
namespace App\Http\Requests\Product\ProductVariant;

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
            "image" => "nullable|string|max:255",
            "album" => "nullable|string",
            "code" => "required|string|max:255",
            "stock" => "required|integer|min:0",
            "sku" => "required|string|max:255",
            "price" => "required|integer|min:0",
            "barcode" => "nullable|integer|min:0",
            "product_id" => "nullable|integer",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function messages(){
        return [];
    }
}
