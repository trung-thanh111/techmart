<?php

namespace App\Http\Requests\User\AddressOrder;

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
            "title" => "nullable|string|max:255",
            "slug" => "required|string|max:255|unique:address_orders,slug",
            "full_name" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "email" => "nullable|email|max:255",
            "address" => "required|string|max:255",
            "state" => "nullable|string|max:100",
            "postal_code" => "required|string|max:20",
            "country_code" => "nullable|string|max:2",
            "is_default" => "boolean",
            "publish" => "required|integer|in:1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => generate_slug($this->title),
            'user_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
