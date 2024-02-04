<?php

namespace App\Http\Requests;

use App\Traits\apiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AddMessageRequest extends FormRequest
{
    //apiResponse trait to use jsonResponse
    use apiResponse;

    /**
     * Determine if the user is authorized to make this request.
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        if ($this->is('api/*')) {
            $response = $this->JsonResponse(422, 'Validation Errors', $validator->errors());
            throw new ValidationException($validator, $response);
        }
    }
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    //The rules applied to the request
    public function rules(): array
    {
        return [
            'email' => 'required',
            'name' => 'required',
            'number' => 'required',
            'message' => 'required'
        ];
    }

    //Function to return the error messages
    public function messages(): array
    {
        return[
            'email.required' => 'Email field is required.',
            'name.required' => 'Name is required.',
            'number.required' => 'Number is required.',
            'message' => 'Message is required.'
        ];
    }

}
