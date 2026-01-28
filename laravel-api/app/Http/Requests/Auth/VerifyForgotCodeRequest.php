<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class VerifyForgotCodeRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ];
    }
}
