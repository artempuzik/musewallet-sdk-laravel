<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MuseWallet\SDK\Enums\CardLevel;

/**
 * Request validation for card application
 * According to MusePay API: POST /v1/card/apply
 */
class ApplyCardRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'max:50'],
            'request_id' => ['required', 'string', 'max:100'], // UUID recommended
            'card_product_id' => ['required', 'string', 'max:50'],
            'card_level' => [
                'required',
                'string',
                Rule::in(array_keys(CardLevel::all()))
            ],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone_area_code' => ['required', 'string', 'max:5'],
            'embossed_name' => ['nullable', 'string', 'max:26'], // Name on card
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
            'user_id.required' => 'User ID is required',
            'request_id.required' => 'Request ID is required (use UUID)',
            'card_product_id.required' => 'Card product ID is required',
            'card_level.required' => 'Card level is required',
            'card_level.in' => 'Card level must be between 1 and 5',
            'phone_number.required' => 'Phone number is required',
            'phone_area_code.required' => 'Phone area code is required',
            'embossed_name.max' => 'Embossed name cannot exceed 26 characters',
        ];
    }
}

