<?php

namespace App\Http\Requests\Post\Post;

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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            "image" => "nullable|sometimes|mimes:{$mimes}|max:{$max_size}",
            'table_of_content' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'view_count' => 'nullable|integer|min:0',
            'post_catalogue_id' => 'nullable|integer|min:0',
            'tag_id' => 'nullable|integer|min:0',
            'publish' => 'nullable|integer|in:0,1,2',
            'user_id' => 'nullable|integer|exists:users,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'post_catalogue_id' => $this->getFirstArrayValue('postCatalogues'),
            'tag_id' => $this->getFirstArrayValue('postTags'),
            'published_at' => $this->published_at ?: ($this->publish === 2 ? now() : null),
        ]);
    }

    /**
     * Lấy giá trị đầu tiên từ mảng một cách an toàn
     * 
     * @param string $field - Tên field chứa mảng
     * @return mixed|null - Giá trị đầu tiên hoặc null
     */
    private function getFirstArrayValue(string $field)
    {
        if (!$this->has($field)) {
            return null;
        }
        $value = $this->input($field);
        if (!is_array($value) || empty($value)) {
            return null;
        }
        $firstValue = reset($value);
        if (is_numeric($firstValue) && $firstValue > 0) {
            return (int) $firstValue;
        }
        return null;
    }

    public function messages()
    {
        return [];
    }
}
