<?php
namespace App\Http\Requests\AgencySystem\Agency;

use App\Enums\Common;
use Illuminate\Support\Str;
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
     * // Rule này sẽ bỏ qua validation slug theo id trong bảng user_catalogues
     * Rule::unique('user_catalogues')->ignore($this->route('user_catalogue'))
     *
     * // prepareForValidation
     * $this->merge([
     *      'slug' => Str::slug($this->name),
     *  ]);
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            "name" => "required|string|max:255|{$sometime}",
            "description" => "nullable|string|{$sometime}",
            "logo" => "nullable|sometimes|mimes:{$mimes}|max:{$max_size}",
            "website" => "nullable|string|max:255|{$sometime}",
            "email" => "nullable|string|email|{$sometime}",
            "phone" => "nullable|string|max:20|{$sometime}",
            "address" => "nullable|string|max:255|{$sometime}",
            "iframe_map" => "nullable|string|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique("agencies", "slug")->ignore($this->route("agency")),
            ],
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'updated_at' => now(),
           ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages(){
        return [];
    }
}
