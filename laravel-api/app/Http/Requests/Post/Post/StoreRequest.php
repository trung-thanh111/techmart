<?php

namespace App\Http\Requests\Post\Post;

use App\Enums\Common;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;
use Nette\Utils\Random;

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
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'album' => 'nullable|array',
            'album.*.path' => 'nullable|string|max:255',
            'extra_album' => 'nullable|array',
            'extra_album.*.path' => 'nullable|string|max:255',
            'table_of_content' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'post_catalogue_id' => 'nullable|integer|min:0',
            'publish' => 'nullable|integer|in:0,1,2',
            'user_id' => 'nullable|integer|exists:users,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'scheme' => 'nullable|string',
            'iframe' => 'nullable|string',
            'published_at' => 'nullable|date',
        ];
    }

    public function prepareForValidation() {
        $this->merge([
            'slug' => Str::slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
            'published_at' => $this->publish === 2 ? now() : null,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
