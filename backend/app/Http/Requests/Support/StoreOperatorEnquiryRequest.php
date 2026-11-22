<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreOperatorEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facility_name' => ['required', 'string', 'max:180'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'location_zone' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:0'], // Honeypot
        ];
    }
}
