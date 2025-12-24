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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "title" => "{$sometime}|required|string|max:255",
            "type" => "{$sometime}|required|string|in:promotion,notification,confirm,form,guide",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique('popups', 'slug')->ignore($this->route('popup')),
            ],
            "content" => "{$sometime}|nullable|string",
            "image" => "{$sometime}|nullable|mimes:{$mimes}|max:{$max_size}",
            "link" => "{$sometime}|nullable|url|max:255",
            "button_text" => "{$sometime}|nullable|string|max:255",
            "form_html" => "{$sometime}|nullable|string",
            "show_delay" => "{$sometime}|nullable|integer|min:0",
            "priority" => "{$sometime}|nullable|boolean",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
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
