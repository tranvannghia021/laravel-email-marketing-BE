<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CampaignTestRequest extends FormRequest
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
            'subject' => 'required|min:6|max:150',
            'image' => 'required|mimes:jpg,jpeg,png,gif',
            'email_body' => 'required',
            'list_email' => 'required',
        ];
    }


    public function messages()
    {
        return [
            'subject.required' => 'Subject campaign is required!',
            'subject.min' => 'Subject at Least 6 Characters!',
            'subject.max' => 'Maximum character subject is 78',
            'image.required' => 'Thumb campaign banner is required!',
            'email_body.required' => 'Email content campaign is required!',
            'list_email.required' => 'list email is required!',

        ];
    }


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
