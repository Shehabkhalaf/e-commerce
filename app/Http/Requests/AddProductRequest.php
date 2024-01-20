<?php

namespace App\Http\Requests;

use App\Traits\apiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddProductRequest extends FormRequest
{
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
            'title' => 'required|unique:products,title',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'stock' => 'required',
            'barcode' => 'required|unique:products,barcode',
            'images' => 'required',
        ];
    }

    //Function to return the error messages
    public function messages(): array
    {
        return[
            'title.required' => 'Title is required',
            'title.unique' => 'Title must be unique',
            'category_id.required' => 'Category id must be combined',
            'description.required' => 'Description is required',
            'price.required' => 'Price is required',
            'discount.required' => 'Discount is required',
            'stock.required' => 'Stock is required',
            'barcode.required' => 'Barcode is required',
            'barcode.unique' => 'Barcode must be unique',
            'images' => 'Images is required'
        ];
    }
}
