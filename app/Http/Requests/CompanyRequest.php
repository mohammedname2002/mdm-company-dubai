<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {


        return [
            'name' => 'required|string|max:255|min:2',
            'trn' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'discount' => 'required|numeric|min:0|max:100',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'The company name is required.',
            'name.string' => 'The name must be a valid text string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.min' => 'The name must be at least 2 characters.',
            'trn.max' => 'The TRN may not exceed 100 characters.',
            'address.max' => 'The address may not exceed 500 characters.',
            'phone.max' => 'The phone may not exceed 50 characters.',
            'discount.required' => 'The discount is required.',
            'discount.numeric' => 'The discount must be a valid number.',
            'discount.min' => 'The discount cannot be less than 0.',
            'discount.max' => 'The discount cannot exceed 100.',
        ];
    }
}
