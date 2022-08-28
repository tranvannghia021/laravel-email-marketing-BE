<?php

namespace App\Http\Requests\Login;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VerifyRequest extends FormRequest
{
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
            'code' => 'required',
            'host' => 'required',
            'shop' => 'required',
            'timestamp' => 'required',
        ];
    }
    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        return [
            'code.required' => 'Login failed, there was an error in the login session',
            'host.required' => 'Login failed, there was an error in the login session',
            'shop.required' => 'Login failed, there was an error in the login session',
            'timestamp.required' => 'Login failed, there was an error in the login session',

        ];
    }

    /**
     * override failedValidation
     *
     * @param  mixed $validator
     * @return response|json
     */
    protected function failedValidation(Validator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'message' => $errors,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
