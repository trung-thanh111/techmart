<?php

namespace App\Http\Requests\Cart\Cart;

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
     * Rule::unique("user_catalogues")->ignore($this->route("user_catalogue"))
     *
     * // prepareForValidation
     * $this->merge([
     *      "slug" => Str::slug($this->name),
     *  ]);
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === "PATCH") ? "sometimes" : "";
        return [
            "customer_id" => "{$sometime}|nullable|exists:users,id",
            "guest_id"    => "{$sometime}|nullable|string|max:64|unique:carts,guest_id",
            "merged_id"   => "{$sometime}|nullable|exists:carts,id",
            "coupon_code" => "{$sometime}|nullable|string|max:50",
            "meta"        => "{$sometime}|nullable|array",
            "publish"     => "{$sometime}|required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'customer_id' => auth(Common::API)->user()->id,
        ]);
    }

    public function messages()
    {
        return [];
    }
}
