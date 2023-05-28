<?php

namespace App\Http\Requests\API;

use App\Traits\ResponsAPI;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    use ResponsAPI;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "email_or_phone" => "required",
            "password" => "required"
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->responseValidation($validator->errors()->toArray(), 'Failed!'));
    }
}
