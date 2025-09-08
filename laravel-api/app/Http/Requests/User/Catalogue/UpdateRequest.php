<?php

namespace App\Http\Requests\User\Catalogue;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        return [
            "name" => "required|string|max:255|{$sometime}",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                // Rule này sẽ bỏ qua validation slug theo id trong bảng user_catalogues
                Rule::unique('user_catalogues')->ignore($this->route('user_catalogue')),

            ],
            "description" => "nullable|string",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
