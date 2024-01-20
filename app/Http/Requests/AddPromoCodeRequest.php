<?php

namespace App\Http\Requests;

use App\Traits\apiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddPromoCodeRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'promocode' => 'required|unique:promocodes,promocode',
            'started_at' => 'required|date|after_or_equal:today',
            'expired_at' => 'required|date|after:started_at',
            'discount' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'promocode.required' => 'Promocde is required',
            'started_at.required' => 'Started at date is required',
            'started_at.date' => 'Start date must be valid date',
            'started_at.after_or_equal' => 'Start date must be in present or future',
            'expired_at.required' => 'Expired at date is required',
            'expired_at.after' => 'Expired date must be after start date',
            'expired_at.date' => 'Expired date must be date'
        ];
    }
}
