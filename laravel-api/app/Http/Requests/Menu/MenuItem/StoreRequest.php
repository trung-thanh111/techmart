<?php

namespace App\Http\Requests\Menu\MenuItem;

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
            "parent_id" => "nullable|integer|exists:menu_items,id",
            "name" => "required|string|max:255",
            "slug" => "required|string|max:255",
            "icon" => "nullable|string|max:255",
            "order" => "nullable|integer",
            "menu_id" => "required|integer|exists:menus,id",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'parent_id' => $this->parent_id ?? 0,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
