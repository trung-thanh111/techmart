<?php

namespace App\Http\Requests\System\Popup;

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
            "title" => "required|string|max:255",
            // fixed 5 types: promotion, notification, confirm, form, guide
            "type" => "required|string|in:promotion,notification,confirm,form,guide",
            "slug" => "required|string|max:255|unique:popups,slug",
            "content" => "nullable|string",
            "image" => "nullable|string|max:255",
            "link" => "nullable|url|max:255",
            "button_text" => "nullable|string|max:255",
            "form_html" => "nullable|string",
            "show_delay" => "nullable|integer|min:0",
            "priority" => "nullable|boolean",
            "publish" => "required|integer|in:0,1,2",
            // conditional requirements to guarantee data completeness per type
            "image" => "required_if:type,promotion|nullable|string|max:255",
            "link" => "required_if:type,promotion|nullable|url|max:255",
            "button_text" => "required_if:type,promotion,confirm|nullable|string|max:255",
            "form_html" => "required_if:type,form|nullable|string",
            "content" => "required_if:type,notification,confirm,guide|nullable|string",
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
