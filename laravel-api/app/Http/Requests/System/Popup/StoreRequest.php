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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "title" => "required|string|max:255",
            "type" => "required|string|in:promotion,notification,confirm,form,guide",
            "slug" => "required|string|max:255|unique:popups,slug",
            "content" => "nullable|string",
            "image" => "nullable|mimes:{$mimes}|max:{$max_size}",
            "link" => "nullable|string|max:255",
            "button_text" => "nullable|string|max:255",
            "form_html" => "nullable|string",
            "show_delay" => "nullable|integer|min:0",
            "priority" => "nullable|boolean",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('title') && empty($this->input('slug'))) {
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
