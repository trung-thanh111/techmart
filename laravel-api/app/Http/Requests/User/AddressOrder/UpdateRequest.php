<?php

namespace App\Http\Requests\User\AddressOrder;

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
        return [
            "title" => "nullable|string|max:255|{$sometime}",
            "full_name" => "required|string|max:255|{$sometime}",
            "phone" => "required|string|max:20|{$sometime}",
            "email" => "nullable|email|max:255|{$sometime}",
            "address" => "required|string|max:255|{$sometime}",
            "state" => "nullable|string|max:100|{$sometime}",
            "city" => "nullable|string|max:100|{$sometime}",
            "postal_code" => "required|string|max:20|{$sometime}",
            "country_code" => "nullable|string|max:2|{$sometime}",
            "is_default" => "boolean|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth(Common::API)->id() ?? auth(Common::Admin)->id(),
            ...($this->has('title') && !$this->has('slug') ? ['slug' => Str::slug($this->title)] : []),
            'publish' => $this->publish ?? 2,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
