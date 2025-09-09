<?php

namespace App\Http\Requests\User\Catalogue;

use App\Http\Requests\BaseRequest;

class BulkStoreRequest extends BaseRequest
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
            "data.*.name" => "required|string|max:255",
            "data.*.slug" => "required|string|unique:user_catalogues,slug",
            "data.*.description" => "nullable|string",
            "data.*.publish" => "required|integer|in:0,1,2",
        ];
    }
}
