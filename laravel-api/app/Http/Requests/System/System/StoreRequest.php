<?php

namespace App\Http\Requests\System\System;

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
            "keyword" => "required|string|max:255",
            "content" => "nullable|string",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
            'created_at' => now(),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
