<?php

namespace App\Http\Requests\Brand\Brand;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        return [
            "name" => "{$sometime}|required|string|max:255",
            "description" => "{$sometime}|nullable|string",
            "logo" => "{$sometime}|nullable|string|max:500",
            "website" => "{$sometime}|nullable|url|max:255",
            "country" => "{$sometime}|nullable|string|max:100",
            "order" => "{$sometime}|nullable|integer|min:0",
            "publish" => "{$sometime}|required|integer|in:1,2",
            "user_id" => "{$sometime}|required|integer|exists:users,id",
            "slug" => [
                "required",
                "string",
                "max:255",
                Rule::unique('brands')->ignore($this->route('brand')),
                "{$sometime}"
            ],
        ];
    }

    public function prepareForValidation()
    {
        // Tự động tạo slug từ name nếu slug không được cung cấp
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
