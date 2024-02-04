<?php

namespace App\Http\Requests;

use App\Traits\apiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MakeOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:30',
            'email' => 'required',
            'address' => 'required',
            'governorate' => 'required|max:20',
            'city' => 'required|max:20',
            'postal' => 'required|max:10',
            'phone' => 'required|max:15',
            'promocode' => 'required',
            'total_price' => 'required',
            'products' => 'required'
        ];
    }
}
