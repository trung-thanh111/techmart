<?php

namespace App\Http\Requests\Banner\BannerPage;

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
            "slug" => "required|string|max:255|unique:banner_pages,slug",
            "code" => "required|string|max:255|unique:banner_pages,code",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'code' => generate_snake_case($this->name),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
