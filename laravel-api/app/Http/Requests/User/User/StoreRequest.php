<?php
namespace App\Http\Requests\User\User;

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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users,email",
            "last_name" => "nullable|string|max:255",
            "first_name" => "nullable|string|max:255",
            "description" => "nullable|string|max:255",
            "birthday" => "nullable|date",
            "gender" => "nullable|string|in:male,female,other",
            "phone" => "nullable|string|max:255",
            "address" => "nullable|string|max:255",
            "password" => "required|string|min:8",
            "publish" => "required|integer|in:0,1,2",
            // "user_catalogues" => "required|array",
            // "user_catalogues.*" => "exists:user_catalogues,id",
            "image" => "sometimes|mimes:{$mimes}|max:{$max_size}"
        ];
    }

    public function prepareForValidation(){

    }

    public function messages(){
        return [

        ];
    }
}
