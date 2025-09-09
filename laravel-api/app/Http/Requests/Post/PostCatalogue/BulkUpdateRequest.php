<?php
namespace App\Http\Requests\Post\PostCatalogue;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class BulkUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * rules slug
     * $rules["data.$key.slug"] = [
     *          'required',
     *           'string',
     *            Rule::unique('user_catalogues', 'slug')->ignore($id),
     * ];
     *
     * @return array
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $sometime = ($method === 'PATCH') ? 'sometimes' : '';

        $rules = [];
        foreach ($this->input('data', []) as $key => $val) {
            $id = $val['id'] ?? null;
            $rules["data.$key.name"] = "required|string|max:255|{$sometime}";
            $rules["data.$key.publish"] = "required|integer|in:0,1,2|{$sometime}";
        }

        return $rules;
    }
}
