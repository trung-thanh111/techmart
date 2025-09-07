<?php
namespace App\Http\Requests\AgencySystem\AgencyWorkingHour;

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
            'day_of_week' => 'required|string|max:255',
            'open_time' => 'nullable',
            'close_time' => 'nullable',
            'publish' => 'required|integer|in:1,2',
        ];
    }

    public function prepareForValidation(){
        // $this->merge([
        //     'slug' => generate_slug($this->name),
        //     'user_id' => auth(Common::API)->user()->id,
        // ]);
    }

    public function messages(){
        return [];
    }
}
