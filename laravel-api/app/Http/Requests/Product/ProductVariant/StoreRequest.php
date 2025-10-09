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
            "name" => "required|string|max:255",
            "url" => "nullable|string",
            "code" => "nullable|string|max:255",
            "stock" => "nullable|integer|min:0",
            "sku" => "nullable|string|max:255",
            "price" => "nullable|numeric|min:0",
            "barcode" => "nullable|string|max:255",
            "attribute_data" => "nullable|array",
            "attributes" => "nullable|array",
            "publish" => "nullable|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'publish' => $this->publish ?? 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function messages(){
        return [];
    }
}
