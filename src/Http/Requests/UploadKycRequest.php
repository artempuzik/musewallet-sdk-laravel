<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MuseWallet\SDK\Enums\DocumentType;

/**
 * Request validation for uploading KYC documents
 * According to MusePay API: POST /v1/carduser/upload-kyc
 */
class UploadKycRequest extends FormRequest
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

            // Individual information
            'individual' => ['required', 'array'],
            'individual.first_name' => ['required', 'string', 'max:50'],
            'individual.last_name' => ['required', 'string', 'max:50'],
            'individual.date_of_birth' => ['required', 'date', 'before:today'],
            'individual.occupation' => ['nullable', 'string', 'max:100'],
            'individual.annual_income' => ['nullable', 'string', 'max:50'],

            // Document information
            'document' => ['required', 'array'],
            'document.type' => [
                'required',
                'string',
                Rule::in(array_keys(DocumentType::all()))
            ],
            'document.number' => ['required', 'string', 'max:50'],
            'document.country' => ['required', 'string', 'size:2'],
            'document.expiry_date' => ['required', 'date', 'after:today'],
            'document.front' => ['required', 'string'], // Base64 encoded
            'document.back' => ['nullable', 'string'], // Base64 encoded
            'document.face' => ['nullable', 'string'], // Base64 encoded (selfie)

            // Address information
            'address' => ['required', 'array'],
            'address.country' => ['required', 'string', 'size:2'],
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
            'individual.required' => 'Individual information is required',
            'individual.date_of_birth.required' => 'Date of birth is required',
            'document.required' => 'Document information is required for KYC',
            'document.type.required' => 'Document type is required',
            'document.type.in' => 'Document type must be 1 (National ID) or 2 (Passport)',
            'document.number.required' => 'Document number is required',
            'document.country.required' => 'Document issuing country is required',
            'document.expiry_date.required' => 'Document expiry date is required',
            'document.expiry_date.after' => 'Document expiry date must be in the future',
            'document.front.required' => 'Front image of document is required',
            'address.required' => 'Address information is required',
            'address.post_code.required' => 'Post code is required',
            'address.details.required' => 'Address details are required',
        ];
    }
}

