<?php

namespace App\Http\Requests\Login;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'domain' => 'required|not_regex:/[^a-z-0-9]/',
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
            'domain.required' => 'Store name is required',
            'domain.not_regex' => 'Store name is invalid',

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
