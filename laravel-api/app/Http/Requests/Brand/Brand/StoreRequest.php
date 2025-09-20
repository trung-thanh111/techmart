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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "name" => "required|string|max:255",
            "slug" => "required|string|max:255|unique:brands,slug",
            "description" => "nullable|string",
            "logo" => "nullable|sometimes|mimes:{$mimes}|max:{$max_size}",
            "website" => "nullable|url|max:255",
            "country" => "nullable|string|max:100",
            "order" => "nullable|integer|min:0",
            "publish" => "required|integer|in:0,1,2",
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
