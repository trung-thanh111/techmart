<?php

namespace App\Http\Requests\Comment\PostComment;

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
            "parent_id"   => "{$sometime}|nullable|exists:post_comments,id",
            "content"     => "{$sometime}|nullable|string",
            "is_hot"      => "{$sometime}|nullable|boolean",
            "user_id"     => "{$sometime}|nullable|exists:users,id",
            "post_id"     => "{$sometime}|nullable|exists:posts,id",
            "publish"     => "{$sometime}|required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'updated_at' => now(),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
