<?php

namespace MuseWallet\SDK\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for getting card information
 * According to MusePay API: POST /v1/card/query
 */
class GetCardInfoRequest extends FormRequest
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
            'card_id' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'string', 'max:50'],
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
            'card_id.required' => 'Card ID is required',
            'user_id.required' => 'User ID is required',
        ];
    }
}

