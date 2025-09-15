<?php
namespace App\Http\Requests\AgencySystem\Agency;

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
            "name" => "required|string|max:255",
            "slug" => "required|string|max:255|unique:agencies,slug",
            "description" => "nullable|string|max:255",
            "logo" => "nullable|max:255",
            "website" => "nullable|string|max:255",
            "email" => "nullable|string|email|max:255",
            "phone" => "nullable|string|max:20",
            "address" => "nullable|string|max:255",
            "iframe_map" => "nullable|string",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'slug' => generate_slug($this->name),
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages(){
        return [];
    }
}
