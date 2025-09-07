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
            "publish" => "required|integer|in:1,2|{$sometime}",
            "email" => [
                "{$sometime}",
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            "password" => "required|string|min:8|{$sometime}",
            "user_catalogues" => "required|array|{$sometime}",
            "user_catalogues.*" => "exists:user_catalogues,id|{$sometime}",
        ];
    }

    public function prepareForValidation(){

    }

    public function messages(){
        return [

        ];
    }
}
