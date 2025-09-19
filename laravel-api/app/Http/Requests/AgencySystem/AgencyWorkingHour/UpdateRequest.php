<?php

namespace App\Http\Requests\AgencySystem\AgencyWorkingHour;

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
            'day_of_week' => "required|string|max:255|{$sometime}",
            'open_time' => "required|{$sometime}",
            'close_time' => "required|{$sometime}",
            'publish' => "required|integer|in:0,1,2|{$sometime}",
        ];
    }

    public function prepareForValidation() {}

    public function messages()
    {
        return [];
    }
}
