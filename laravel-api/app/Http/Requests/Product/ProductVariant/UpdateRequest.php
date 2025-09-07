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
            "image" => "{$sometime}|nullable|string|max:255",
            "album" => "{$sometime}|nullable|string",
            "code" => "{$sometime}|required|string|max:255",
            "stock" => "{$sometime}|required|integer|min:0",
            "sku" => "{$sometime}|required|string|max:255",
            "price" => "{$sometime}|required|integer|min:0",
            "weight" => "{$sometime}|nullable|integer|min:0",
            "barcode" => "{$sometime}|nullable|integer|min:0",
            "product_id" => "{$sometime}|nullable|integer",
            "publish" => "{$sometime}|required|integer|in:1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages(){
        return [];
    }
}
