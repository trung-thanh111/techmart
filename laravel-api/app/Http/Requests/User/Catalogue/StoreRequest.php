<?php

namespace App\Http\Requests\User\Catalogue;

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
            "name" => "required|string|max:255",
            "slug" => "required|string|unique:user_catalogues,slug",
            "description" => "nullable|string",
            "publish" => "required|integer|in:2,1,0",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->name),
        ]);
    }
}
