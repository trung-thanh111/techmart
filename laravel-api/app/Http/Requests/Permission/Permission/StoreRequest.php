<?php

namespace App\Http\Requests\Permission\Permission;

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
            "name" => "required|string|max:255",
            "value" => "required|integer|min:0",
            "module" => "required|string|max:255",
            "title" => "nullable|string|max:255",
            "user_id" => "nullable|integer|exists:users,id",
            "publish" => "required|integer|in:1,2",
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
