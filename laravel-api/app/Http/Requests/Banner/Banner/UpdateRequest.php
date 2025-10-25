<?php

namespace App\Http\Requests\Banner\Banner;

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
            "name" => "required|string|max:255|{$sometime}",
            "slug" => [
                "{$sometime}",
                "required",
                "string",
                "max:255",
                Rule::unique("banners", "slug")->ignore($this->route("banner")),
            ],
            "album" => "nullable|sometimes|array|{$sometime}",
            "page_id" => "required|integer|min:0|{$sometime}",
            "position_id" => "required|integer|min:0|{$sometime}",
            "start_at" => "nullable|date|{$sometime}",
            "end_at" => "nullable|date|after_or_equal:start_at|{$sometime}",
            "order" => "nullable|integer|{$sometime}",
            "link_url" => "nullable|string|max:255|{$sometime}",
            "is_hot" => "nullable|boolean|{$sometime}",
            "publish" => "required|integer|in:0,1,2|{$sometime}",
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            "user_id" => auth(Common::API)->user()->id,
            "updated_at" => now(),
            ...($this->has("name") && !$this->has("slug") ? ["slug" => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
