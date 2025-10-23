<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MuseWallet\SDK\Enums\DocumentType;

/**
 * Request validation for creating a card holder
 * According to MusePay API: POST /v1/carduser/create
 */
class CreateCardHolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'user_xid' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'user_name' => ['nullable', 'string', 'max:100'],

            // Individual information
            'individual' => ['required', 'array'],
            'individual.first_name' => ['required', 'string', 'max:50'],
            'individual.last_name' => ['required', 'string', 'max:50'],
            'individual.date_of_birth' => ['required', 'date', 'before:today'],
            'individual.occupation' => ['nullable', 'string', 'max:100'],
            'individual.annual_income' => ['nullable', 'string', 'max:50'],

            // Document information (optional for initial creation)
            'document' => ['nullable', 'array'],
            'document.type' => [
                'required_with:document',
                'string',
                Rule::in(array_keys(DocumentType::all()))
            ],
            'document.number' => ['required_with:document', 'string', 'max:50'],
            'document.country' => ['required_with:document', 'string', 'size:2'],
            'document.expiry_date' => ['required_with:document', 'date', 'after:today'],
            'document.front' => ['nullable', 'string'], // Base64 encoded
            'document.back' => ['nullable', 'string'], // Base64 encoded
            'document.face' => ['nullable', 'string'], // Base64 encoded (selfie)

            // Address information
            'address' => ['required', 'array'],
            'address.country' => ['required', 'string', 'size:2'], // ISO country code
            'address.city' => ['required', 'string', 'max:50'],
            'address.post_code' => ['required', 'string', 'max:20'],
            'address.details' => ['required', 'string', 'max:200'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_xid.required' => 'User external ID is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'individual.required' => 'Individual information is required',
            'individual.first_name.required' => 'First name is required',
            'individual.last_name.required' => 'Last name is required',
            'individual.date_of_birth.required' => 'Date of birth is required',
            'individual.date_of_birth.before' => 'Date of birth must be in the past',
            'document.type.in' => 'Document type must be 1 (National ID) or 2 (Passport)',
            'document.expiry_date.required_with' => 'Document expiry date is required',
            'document.expiry_date.after' => 'Document expiry date must be in the future',
            'document.country.required_with' => 'Document issuing country is required',
            'address.required' => 'Address information is required',
            'address.country.required' => 'Country is required',
            'address.country.size' => 'Country must be a 2-letter ISO code',
            'address.city.required' => 'City is required',
            'address.post_code.required' => 'Post code is required',
            'address.details.required' => 'Address details are required',
        ];
    }
}

