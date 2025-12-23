<?php

namespace App\Http\Requests\System\Popup;

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
            "title" => "required|string|max:255|{$sometime}",
            "type" => "{$sometime}required|string|in:promotion,notification,confirm,form,guide",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique('popups', 'slug')->ignore($this->route('popup')),
            ],
            "content" => "nullable|string|{$sometime}",
            "image" => "nullable|string|max:255|{$sometime}",
            "link" => "nullable|url|max:255|{$sometime}",
            "button_text" => "nullable|string|max:255|{$sometime}",
            "form_html" => "nullable|string|{$sometime}",
            "show_delay" => "nullable|integer|min:0|{$sometime}",
            "priority" => "nullable|boolean|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",

            "image" => "{$sometime}required_if:type,promotion|nullable|string|max:255",
            "link" => "{$sometime}required_if:type,promotion|nullable|url|max:255",
            "button_text" => "{$sometime}required_if:type,promotion,confirm|nullable|string|max:255",
            "form_html" => "{$sometime}required_if:type,form|nullable|string",
            "content" => "{$sometime}required_if:type,notification,confirm,guide|nullable|string",
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => generate_slug($this->input('title')),
            ]);
        }
    }

    public function messages()
    {
        return [];
    }
}
