<?php

namespace App\Http\Requests\Banner\Banner;

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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'type' => 'required|in:homepage,category,flash_sale,popup,custom',
            'position' => 'required|string|max:100',
            'device' => 'required|in:all,desktop,tablet,mobile',
            'display_mode' => 'required|in:permanent,scheduled',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'sort_order' => 'nullable|integer',
            'link_url' => 'nullable|string|max:255',
            'width' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:50',
            'alt' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:255',
            'loading' => 'nullable|in:lazy,eager,auto',
            'publish' => 'required|integer|in:0,1,2',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
