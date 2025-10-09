<?php
namespace App\Http\Requests\Product\ProductVariant;

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
            "url" => "{$sometime}|nullable|string",
            "code" => "{$sometime}|required|string|max:255",
            "stock" => "{$sometime}|required|integer|min:0",
            "sku" => "{$sometime}|required|string|max:255",
            "price" => "{$sometime}|required|integer|min:0",
            "barcode" => "{$sometime}|nullable|integer|min:0",
            "attribute_data" => "{$sometime}|required|array",
            "attributes" => "{$sometime}|required|array",
            "product_id" => "{$sometime}|nullable|integer",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'updated_at' => now(),
        ]);
    }

    public function messages(){
        return [];
    }
}
