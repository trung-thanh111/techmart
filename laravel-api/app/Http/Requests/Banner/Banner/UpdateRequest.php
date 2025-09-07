<?php

namespace App\Http\Requests\Banner\Banner;

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
            'name' => "required|string|max:255|{$sometime}",
            'slug' => "nullable|string|max:255|{$sometime}",
            'image' => "nullable|string|max:255|{$sometime}",
            'type' => "required|in:homepage,category,flash_sale,popup,custom|{$sometime}",
            'position' => "required|string|max:100|{$sometime}",
            'device' => "required|in:all,desktop,tablet,mobile|{$sometime}",
            'display_mode' => "required|in:permanent,scheduled|{$sometime}",
            'start_at' => "nullable|date|{$sometime}",
            'end_at' => "nullable|date|after_or_equal:start_at|{$sometime}",
            'sort_order' => "nullable|integer|{$sometime}",
            'link_url' => "nullable|string|max:255|{$sometime}",
            'width' => "nullable|string|max:50|{$sometime}",
            'height' => "nullable|string|max:50|{$sometime}",
            'alt' => "nullable|string|max:255|{$sometime}",
            'class' => "nullable|string|max:255|{$sometime}",
            'loading' => "nullable|in:lazy,eager,auto|{$sometime}",
            'publish' => "required|integer|in:0,1,2|{$sometime}",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'updated_at' => now(),
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
