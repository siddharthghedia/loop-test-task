<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    public function failedValidation(Validator $validator)
    {
        $messages = [];
        foreach ($validator->errors()->messages() as $key => $message) {
            $messages[$key] = $message[0];
        }

        $response = new JsonResponse(['success' => false, 'errors' => $messages], 422);

        throw (new ValidationException($validator, $response))->status(422);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
