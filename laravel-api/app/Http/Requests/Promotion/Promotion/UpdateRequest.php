<?php

namespace App\Http\Requests\Promotion\Promotion;

use App\Enums\Promotion;
use Illuminate\Support\Str;
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
        $mimes = implode(',', config('upload.image.allowed_type'));
        $max_size = config('upload.image.max_size');
        return [
            'name' => "{$sometime}|required|string|max:255",
            'code' => ["{$sometime}", 'nullable', 'string', 'max:100', \Illuminate\Validation\Rule::unique('promotions', 'code')->ignore($this->route('promotion'))],
            'slug' => ["{$sometime}", 'required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('promotions', 'slug')->ignore($this->route('promotion'))],
            'description' => "{$sometime}|nullable|string",
            "image" => "nullable|{$sometime}|mimes:{$mimes}|max:{$max_size}",
            'type' => "{$sometime}|required|string|in:" . implode(',', Promotion::AVAILABLE_TYPES),
            'priority' => "{$sometime}|nullable|integer|min:0",
            'promotion_catalogue_id' => "{$sometime}|nullable|integer|exists:promotion_catalogues,id",
            'can_combine' => "{$sometime}|nullable|boolean",
            'combinable_with' => "{$sometime}|nullable|array",
            'combinable_with.*' => "{$sometime}|integer|exists:promotions,id",
            'usage_limit' => "{$sometime}|nullable|integer|min:1",
            'usage_per_customer' => "{$sometime}|nullable|integer|min:1",
            'start_date' => "{$sometime}|nullable|date|before:end_date",
            'end_date' => "{$sometime}|nullable|date|after:start_date",
            'publish' => "{$sometime}|required|integer|in:" . implode(',', Promotion::AVAILABLE_PUBLISH_STATUS),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            ...($this->has('name') && !$this->has('slug') ? ['slug' => Str::slug($this->name)] : [])
        ]);
    }

    public function messages()
    {
        return [];
    }
}
