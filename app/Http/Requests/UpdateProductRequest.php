<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'company_id' => 'required|integer|exists:companies,id',
            'product_name' => 'required|string|max:1000|min:3',
            'price' => 'required|numeric|min:0|max:100000',
            'quantity' => 'required|integer|min:0|max:100000',
            'free_items' => 'nullable|integer|min:0|max:100000',
            'vat' => 'required|numeric|min:0|max:100',
            'date_of_create' => 'required|date',
        ];
    }


    public function messages()
    {
        return [
            // Product Name
            'product_name.required' => 'The product name is required.',
            'product_name.max' => 'The product name must not exceed 1000 characters.',
            'product_name.min' => 'The product name must be at least 3 characters.',
            'product_name.string' => 'The product name must be a valid text string.',

            // Price
            'price.required' => 'The price is required.',
            'price.max' => 'The price may not exceed 100,000.',
            'price.min' => 'The price cannot be negative.',
            'price.numeric' => 'The price must be a valid number.',

            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'The quantity must be at least 0.',
            'quantity.max' => 'The quantity may not exceed 100,000.',

            // VAT
            'vat.required' => 'The VAT is required.',
            'vat.max' => 'The VAT may not exceed 100%.',
            'vat.min' => 'The VAT cannot be negative.',
            'vat.numeric' => 'The VAT must be a valid number.',

            // Date of Create
            'date_of_create.required' => 'The date of create is required.',
            'date_of_create.date' => 'The date of create must be a valid date format.',

            // Company
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company is invalid.',

            // Free Items
            'free_items.integer' => 'Free items must be a whole number.',
            'free_items.min' => 'Free items cannot be negative.',
            'free_items.max' => 'Free items may not exceed 100,000.',
        ];
    }
}
