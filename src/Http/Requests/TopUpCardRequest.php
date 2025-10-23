<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MuseWallet\SDK\Enums\Currency;

/**
 * Request validation for card top-up
 * According to MusePay API: POST /v1/cardaccount/topup
 */
class TopUpCardRequest extends FormRequest
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
            'request_id' => ['required', 'string', 'max:100'], // UUID recommended
            'card_id' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => [
                'required',
                'string',
                Rule::in(array_keys(Currency::all()))
            ],
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
            'request_id.required' => 'Request ID is required (use UUID)',
            'card_id.required' => 'Card ID is required',
            'user_id.required' => 'User ID is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0.01',
            'currency.required' => 'Currency is required',
            'currency.in' => 'Unsupported currency. Use one of: USDT_TRC20, USDT_ERC20, USDC_ERC20, etc.',
        ];
    }
}

