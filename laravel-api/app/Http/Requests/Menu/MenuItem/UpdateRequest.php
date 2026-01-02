<?php

namespace App\Http\Requests\Menu\MenuItem;

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
            "parent_id" => "{$sometime}|nullable|integer|exists:menu_items,id",
            "name" => "{$sometime}|required|string|max:255",
            "slug" => [
                "{$sometime}",
                "string",
                "max:255",
                Rule::unique('menu_items')->ignore($this->route('menu_item'))->where(function ($query) {
                    return $query->where('menu_id', $this->menu_id ?? $this->route('menu_item')->menu_id);
                })
            ],
            "icon" => "{$sometime}|nullable|string|max:255",
            "order" => "{$sometime}|nullable|integer",
            "menu_id" => "{$sometime}|required|integer|exists:menus,id",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : []),
            'parent_id' => $this->parent_id ?? 0,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
