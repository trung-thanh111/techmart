<?php
namespace App\Http\Requests\Post\Tag;

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
            "description" => "nullable|string|max:500",
            "color" => "nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/",
            "publish" => "required|integer|in:1,2",
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
