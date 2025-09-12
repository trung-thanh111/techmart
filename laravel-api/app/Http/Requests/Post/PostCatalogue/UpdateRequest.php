<?php

namespace App\Http\Requests\Post\PostCatalogue;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Enums\Common;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "name" => "required|string|max:255|{$sometime}",
            "parent_id" => "nullable|integer|min:0|{$sometime}",
            "description" => "nullable|string|max:500|{$sometime}",
            "image" => "sometimes|mimes:{$mimes}|max:{$max_size}",
            "is_hot" => "nullable|boolean",
            "order" => "nullable|integer|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
            "meta_title" => "nullable|string|max:255|{$sometime}",
            "meta_keyword" => "nullable|string|max:255|{$sometime}",
            "meta_description" => "nullable|string|max:500|{$sometime}",
            "published_at" => "nullable|date|{$sometime}",
            "slug" => [
                "required",
                "string",
                Rule::unique('post_catalogues')->ignore($this->route('post_catalogue')),
                "{$sometime}"
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'published_at' => $this->publish === 2 ? now() : null,
            'is_hot' => $this->boolean('is_hot'),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
