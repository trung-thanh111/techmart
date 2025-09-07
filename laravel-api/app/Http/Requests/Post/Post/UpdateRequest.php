<?php

namespace App\Http\Requests\Post\Post;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        return [
            "name" => "{$sometime}|required|string|max:255",
            "description" => "{$sometime}|nullable|string|max:500",
            "content" => "{$sometime}|nullable|string",
            "image" => "{$sometime}|nullable|string|max:255",
            "icon" => "{$sometime}|nullable|string|max:255",
            "album" => "{$sometime}|nullable|array",
            "album.*.path" => "{$sometime}|nullable|string|max:255",
            "extra_album" => "{$sometime}|nullable|array",
            "extra_album.*.path" => "{$sometime}|nullable|string|max:255",
            "table_of_content" => "{$sometime}|nullable|boolean",
            "order" => "{$sometime}|nullable|integer|min:0",
            "post_catalogue_id" => "{$sometime}|nullable|integer|min:0",
            "publish" => "{$sometime}|nullable|integer|in:0,1,2",
            "user_id" => "{$sometime}|nullable|integer|exists:users,id",
            "meta_title" => "{$sometime}|nullable|string|max:255",
            "meta_keyword" => "{$sometime}|nullable|string|max:255",
            "meta_description" => "{$sometime}|nullable|string|max:500",
            "scheme" => "{$sometime}|nullable|string",
            "iframe" => "{$sometime}|nullable|string",
            "published_at" => "{$sometime}|nullable|date",
            "slug" => [
                "required",
                "string",
                Rule::unique('posts')->ignore($this->route('post')),
                "{$sometime}"
            ],
        ];
    }

    public function prepareForValidation() {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    public function messages()
    {
        return [];
    }
}
