<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'topic' => ['required', 'string', 'in:general,report_discrepancy,pricing_issue,booking_help,other'],
            'facility_reference' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['nullable', 'string', 'max:0'], // Honeypot field
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name.',
            'email.required' => 'A valid email address is required so we can reply.',
            'topic.required' => 'Please select an inquiry topic.',
            'message.required' => 'Please explain your query or report.',
            'message.min' => 'Please provide at least 10 characters in your message.',
        ];
    }
}
