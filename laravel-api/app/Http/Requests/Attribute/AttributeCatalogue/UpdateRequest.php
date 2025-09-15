<?php
namespace App\Http\Requests\Attribute\AttributeCatalogue;

use App\Enums\Common;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

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
            "name" => "{$sometime}|required|string|max:255",
            "description" => "{$sometime}|nullable|string|max:255",
            "order" => "{$sometime}|nullable|integer|min:0",
            "publish" => "{$sometime}|required|integer|in:0,1,2",
            "description" => "{$sometime}|nullable|string|max:255",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique('attribute_catalogues')->ignore($this->route('attribute_catalogue')),
            ],
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'user_id' => auth(Common::API)->user()->id,
           ...($this->has('name') && !$this->has('slug') ? ['slug' => generate_slug($this->name)] : [])
        ]);
    }

    public function messages(){
        return [];
    }
}
