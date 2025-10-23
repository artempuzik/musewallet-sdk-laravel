<?php

namespace MuseWallet\SDK\Tests\Unit;

use MuseWallet\SDK\Http\Requests\{
    CreateCardHolderRequest,
    ApplyCardRequest,
    QueryApplyResultRequest,
    GetCardInfoRequest,
    ActivateCardRequest,
    TopUpCardRequest,
    UploadKycRequest,
    GenerateKycLinkRequest
};
use MuseWallet\SDK\Enums\{CardLevel, Currency, DocumentType};
use MuseWallet\SDK\Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class RequestValidationTest extends TestCase
{
    public function test_create_card_holder_validates_required_fields()
    {
        $request = new CreateCardHolderRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_xid', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('individual', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
    }

    public function test_create_card_holder_validates_email_format()
    {
        $request = new CreateCardHolderRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'email' => 'invalid-email'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_create_card_holder_validates_document_type_enum()
    {
        $request = new CreateCardHolderRequest();
        $rules = $request->rules();

        // Invalid document type
        $validator = Validator::make([
            'document' => [
                'type' => '999', // Invalid
                'number' => 'P123456'
            ]
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document.type', $validator->errors()->toArray());

        // Valid document types (as strings, since keys are strings)
        foreach (array_keys(DocumentType::all()) as $validType) {
            $validator = Validator::make([
                'user_xid' => 'test',
                'email' => 'test@example.com',
                'individual' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'date_of_birth' => '1990-01-01'
                ],
                'document' => [
                    'type' => (string)$validType,  // Ensure it's a string
                    'number' => 'P123456',
                    'country' => 'US',
                    'expiry_date' => '2030-12-31'
                ],
                'address' => [
                    'country' => 'US',
                    'city' => 'NYC',
                    'post_code' => '10001',
                    'details' => '123 Main St'
                ]
            ], $rules);

            $documentErrors = $validator->errors()->get('document.type');
            $this->assertEmpty(
                $documentErrors,
                "Document type {$validType} should be valid. Errors: " . json_encode($documentErrors)
            );
        }
    }

    public function test_apply_card_validates_required_fields()
    {
        $request = new ApplyCardRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('request_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('card_product_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('card_level', $validator->errors()->toArray());
    }

    public function test_apply_card_validates_card_level_enum()
    {
        $request = new ApplyCardRequest();
        $rules = $request->rules();

        // Invalid card level
        $validator = Validator::make([
            'card_level' => '999'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('card_level', $validator->errors()->toArray());

        // Valid card levels (as strings, since keys are strings)
        foreach (array_keys(CardLevel::all()) as $validLevel) {
            $validator = Validator::make([
                'user_id' => 'user_123',
                'request_id' => 'req_123',
                'card_product_id' => 'prod_123',
                'card_level' => (string)$validLevel,  // Ensure it's a string
                'phone_number' => '1234567890',
                'phone_area_code' => '1'
            ], $rules);

            $levelErrors = $validator->errors()->get('card_level');
            $this->assertEmpty(
                $levelErrors,
                "Card level {$validLevel} should be valid. Errors: " . json_encode($levelErrors)
            );
        }
    }

    public function test_topup_card_validates_currency_enum()
    {
        $request = new TopUpCardRequest();
        $rules = $request->rules();

        // Invalid currency
        $validator = Validator::make([
            'currency' => 'INVALID_CURRENCY'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('currency', $validator->errors()->toArray());

        // Valid currencies
        foreach (array_keys(Currency::all()) as $validCurrency) {
            $validator = Validator::make([
                'request_id' => 'req_123',
                'card_id' => 'card_123',
                'user_id' => 'user_123',
                'amount' => '100.00',
                'currency' => $validCurrency
            ], $rules);

            $this->assertFalse(
                $validator->errors()->has('currency'),
                "Currency {$validCurrency} should be valid"
            );
        }
    }

    public function test_topup_card_validates_amount()
    {
        $request = new TopUpCardRequest();
        $rules = $request->rules();

        // Amount too low
        $validator = Validator::make([
            'amount' => '0.00'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('amount', $validator->errors()->toArray());

        // Negative amount
        $validator = Validator::make([
            'amount' => '-10.00'
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('amount', $validator->errors()->toArray());

        // Valid amount
        $validator = Validator::make([
            'request_id' => 'req_123',
            'card_id' => 'card_123',
            'user_id' => 'user_123',
            'amount' => '100.00',
            'currency' => 'USDT_TRC20'
        ], $rules);

        $this->assertFalse($validator->errors()->has('amount'));
    }

    public function test_query_apply_result_validates_required_fields()
    {
        $request = new QueryApplyResultRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('request_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }

    public function test_get_card_info_validates_required_fields()
    {
        $request = new GetCardInfoRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('card_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }

    public function test_activate_card_validates_required_fields()
    {
        $request = new ActivateCardRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('card_id', $validator->errors()->toArray());
    }

    public function test_upload_kyc_validates_required_fields()
    {
        $request = new UploadKycRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_xid', $validator->errors()->toArray());
        $this->assertArrayHasKey('individual', $validator->errors()->toArray());
        $this->assertArrayHasKey('document', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
    }

    public function test_upload_kyc_validates_document_type_enum()
    {
        $request = new UploadKycRequest();
        $rules = $request->rules();

        // Invalid document type
        $validator = Validator::make([
            'user_xid' => 'user_123',
            'individual' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '1990-01-01'
            ],
            'document' => [
                'type' => '999',
                'number' => 'P123456',
                'country' => 'US',
                'expiry_date' => '2030-12-31',
                'front' => 'base64_image'
            ],
            'address' => [
                'country' => 'US',
                'city' => 'NYC',
                'post_code' => '10001',
                'details' => '123 Main St'
            ]
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document.type', $validator->errors()->toArray());
    }

    public function test_generate_kyc_link_validates_required_fields()
    {
        $request = new GenerateKycLinkRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_xid', $validator->errors()->toArray());
    }

    public function test_all_requests_pass_validation_with_valid_data()
    {
        // CreateCardHolderRequest
        $createCardHolderRequest = new CreateCardHolderRequest();
        $validator = Validator::make([
            'user_xid' => 'user_123',
            'email' => 'test@example.com',
            'individual' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '1990-01-01'
            ],
            'address' => [
                'country' => 'US',
                'city' => 'New York',
                'post_code' => '10001',
                'details' => '123 Main St'
            ]
        ], $createCardHolderRequest->rules());
        $this->assertFalse($validator->fails());

        // ApplyCardRequest
        $applyCardRequest = new ApplyCardRequest();
        $validator = Validator::make([
            'user_id' => 'user_123',
            'request_id' => 'req_123',
            'card_product_id' => 'prod_123',
            'card_level' => '1',
            'phone_number' => '1234567890',
            'phone_area_code' => '1'
        ], $applyCardRequest->rules());
        $this->assertFalse($validator->fails());

        // TopUpCardRequest
        $topUpRequest = new TopUpCardRequest();
        $validator = Validator::make([
            'request_id' => 'req_123',
            'card_id' => 'card_123',
            'user_id' => 'user_123',
            'amount' => '100.00',
            'currency' => 'USDT_TRC20'
        ], $topUpRequest->rules());
        $this->assertFalse($validator->fails());
    }
}

