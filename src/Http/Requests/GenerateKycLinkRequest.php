<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for generating KYC link
 * According to MusePay API: POST /v1/carduser/kyc-link
 */
class GenerateKycLinkRequest extends FormRequest
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
        ];
    }
}

