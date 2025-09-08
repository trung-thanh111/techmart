<?php
namespace App\Http\Requests\Faq\Faq;

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
            "question" => "required|string|max:255",
            "answer" => "required|string",
            "sort_order" => "nullable|integer|min:0",
            "is_featured" => "nullable|boolean",
            "faq_catalogue_id" => "required|integer|exists:faq_catalogues,id",
            "publish" => "required|integer|in:0,1,2",
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'slug' => generate_slug($this->question),
            'user_id' => auth(Common::API)->user()->id,
            'sort_order' => $this->sort_order ?? 0,
        ]);
    }

    public function messages(){
        return [];
    }
}
