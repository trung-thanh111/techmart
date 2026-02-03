<?php

namespace App\Http\Requests\User\User;

use App\Http\Requests\BaseRequest;
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
     * // Rule này sẽ bỏ qua validation slug theo id trong bảng user_catalogues
     * Rule::unique('user_catalogues')->ignore($this->route('user_catalogue'))
     *
     * // prepareForValidation
     * $this->merge([
     *      'slug' => Str::slug($this->name),
     *  ]);
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        return [
            "name" => "required|string|max:255|{$sometime}",
            "last_name" => "nullable|string|max:255|{$sometime}",
            "first_name" => "nullable|string|max:255|{$sometime}",
            "description" => "nullable|string|max:255|{$sometime}",
            "birthday" => "nullable|date|{$sometime}",
            "gender" => "nullable|string|in:male,female,other|{$sometime}",
            "phone" => "nullable|string|max:255|{$sometime}",
            "street" => "nullable|string|max:255|{$sometime}",
            "city" => "nullable|string|max:255|{$sometime}",
            "ward" => "nullable|string|max:255|{$sometime}",
            "user_catalogue_id" => "nullable|integer|exists:user_catalogues,id|{$sometime}",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
            "email" => [
                "{$sometime}",
                "required",
                "email",
                Rule::unique("users")->ignore($this->route("user"))
            ],
            "password" => "string|min:8|{$sometime}",
            // "user_catalogues" => "required|array|{$sometime}",
            // "user_catalogues.*" => "exists:user_catalogues,id|{$sometime}",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'updated_at' => now(),
        ]);
    }

    public function messages()
    {
        return [];
    }
}
