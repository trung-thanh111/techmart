<?php

namespace App\Http\Requests\Menu\Menu;

use App\Enums\Common;
use App\Enums\Menu as MenuEnum;
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
            "slug" => "required|string|max:255|unique:menus,slug",
            "position" => "required|string|in:" . implode(',', MenuEnum::getPositionValues()),
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
