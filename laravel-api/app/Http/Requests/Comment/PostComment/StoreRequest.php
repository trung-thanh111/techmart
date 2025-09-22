<?php
namespace App\Http\Requests\Comment\PostComment;

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
            'parent_id'   => 'nullable|exists:post_comments,id',
            'content'     => 'nullable|string',                   // longText
            'is_hot'      => 'nullable|boolean',
            'user_id'     => 'nullable|exists:users,id',
            'post_id'     => 'nullable|exists:posts,id',
            'publish'     => 'required|integer|in:0,1,2',
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages(){
        return [];
    }
}
