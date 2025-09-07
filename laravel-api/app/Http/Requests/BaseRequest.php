<?php

namespace App\Http\Requests;

use App\Http\Resources\ApiResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator) {
        $errors = collect($validator->errors()->toArray())
            ->map(fn($messages) => $messages[0])
            ->toArray();

        $resource = ApiResource::error($errors, Lang::get('message.validation_failed'), Response::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($resource);
    }

    public function prepareForValidation()
    {
        $this->merge([
            'data' => $this->generateSlugs($this->input('data', [])),
        ]);
    }

    private function generateSlugs(array $data = []): array
    {
        return array_map(function ($item) {
            $item['slug'] = Str::slug($item['name']);
            return $item;
        }, $data);
    }
}
