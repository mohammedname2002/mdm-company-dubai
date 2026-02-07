<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
            'status' => 'required|in:paid,unpaid',
            'company_id' => 'required|integer|exists:companies,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'The invoice status is required.',
            'status.in' => 'The status must be either paid or unpaid.',
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company is invalid.',
            'from.required' => 'The start date is required.',
            'from.date' => 'The start date must be a valid date.',
            'to.required' => 'The end date is required.',
            'to.date' => 'The end date must be a valid date.',
            'to.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }
}
