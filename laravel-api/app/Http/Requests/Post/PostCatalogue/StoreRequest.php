<?php

namespace App\Http\Requests\Post\PostCatalogue;

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
            "name" => "nullable|string|max:255",
            "parent_id" => "nullable|integer|min:0",
            "description" => "nullable|string|max:500",
            "content" => "nullable|string",
            "image" => "sometimes|mimes:{$mimes}|max:{$max_size}",
            "is_hot" => "nullable|boolean",
            "order" => "nullable|integer",
            "publish" => "required|integer|in:0,1,2",
            "published_at" => "nullable|date",
            "meta_title" => "nullable|string|max:255",
            "meta_keyword" => "nullable|string|max:255",
            "meta_description" => "nullable|string|max:1000"
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'published_at' => $this->publish === 2 ? now() : null,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
