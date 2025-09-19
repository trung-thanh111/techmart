<?php

namespace App\Http\Requests\Faq\Faq;

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
            "question" => "required|string|max:255|{$sometime}",
            "answer" => "required|string|{$sometime}",
            "order" => "nullable|integer|min:0|{$sometime}",
            "is_hot" => "nullable|boolean|{$sometime}",
            "faq_catalogue_id" => "required|integer|exists:faq_catalogues,id|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique("faqs", "slug")->ignore($this->route("faq")),
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            ...($this->has('question') && !$this->has('slug') ? ['slug' => Str::slug($this->question)] : []),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
