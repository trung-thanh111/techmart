<?php

namespace App\Http\Requests\Brand\Brand;

use App\Enums\Common;
use Illuminate\Support\Str;
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
            "description" => "nullable|string",
            "logo" => "nullable",
            "website" => "nullable|url|max:255",
            "country" => "nullable|string|max:100",
            "order" => "nullable|integer|min:0",
            "publish" => "required|integer|in:1,2",
            "user_id" => "required|integer|exists:users,id",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
