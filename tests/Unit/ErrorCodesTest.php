<?php

namespace MuseWallet\SDK\Tests\Unit;

use MuseWallet\SDK\Services\MuseWalletErrorCodes;
use MuseWallet\SDK\Tests\TestCase;

class ErrorCodesTest extends TestCase
{
    public function test_returns_all_error_codes()
    {
        $errors = MuseWalletErrorCodes::all();

        $this->assertIsArray($errors);
        $this->assertArrayHasKey(MuseWalletErrorCodes::SUCCESS, $errors);
        $this->assertArrayHasKey(MuseWalletErrorCodes::BAD_REQUEST, $errors);
        $this->assertArrayHasKey(MuseWalletErrorCodes::SYSTEM_ERROR, $errors);
    }

    public function test_gets_error_information()
    {
        $error = MuseWalletErrorCodes::get(MuseWalletErrorCodes::BAD_REQUEST);

        $this->assertIsArray($error);
        $this->assertArrayHasKey('message', $error);
        $this->assertArrayHasKey('description', $error);
        $this->assertArrayHasKey('suggestion', $error);
    }

    public function test_returns_unknown_error_for_invalid_code()
    {
        $error = MuseWalletErrorCodes::get('INVALID_CODE');

        $this->assertEquals('Unknown Error', $error['message']);
        $this->assertStringContainsString('INVALID_CODE', $error['description']);
    }

    public function test_identifies_success_code()
    {
        $this->assertTrue(MuseWalletErrorCodes::isSuccess(MuseWalletErrorCodes::SUCCESS));
        $this->assertFalse(MuseWalletErrorCodes::isSuccess(MuseWalletErrorCodes::BAD_REQUEST));
        $this->assertFalse(MuseWalletErrorCodes::isSuccess(MuseWalletErrorCodes::SYSTEM_ERROR));
    }

    public function test_identifies_retryable_errors()
    {
        $this->assertTrue(MuseWalletErrorCodes::isRetryable(MuseWalletErrorCodes::SYSTEM_ERROR));
        $this->assertTrue(MuseWalletErrorCodes::isRetryable(MuseWalletErrorCodes::WRONG_TIMESTAMP_OR_NONCE));
        $this->assertFalse(MuseWalletErrorCodes::isRetryable(MuseWalletErrorCodes::BAD_REQUEST));
    }

    public function test_identifies_errors_requiring_user_action()
    {
        $this->assertTrue(MuseWalletErrorCodes::requiresUserAction(MuseWalletErrorCodes::KYC_LEVEL_LOW));
        $this->assertTrue(MuseWalletErrorCodes::requiresUserAction(MuseWalletErrorCodes::INSUFFICIENT_BALANCE));
        $this->assertFalse(MuseWalletErrorCodes::requiresUserAction(MuseWalletErrorCodes::SYSTEM_ERROR));
    }

    public function test_formats_error_response()
    {
        $formatted = MuseWalletErrorCodes::formatError(
            MuseWalletErrorCodes::BAD_REQUEST,
            'Additional context'
        );

        $this->assertIsArray($formatted);
        $this->assertEquals(MuseWalletErrorCodes::BAD_REQUEST, $formatted['code']);
        $this->assertArrayHasKey('message', $formatted);
        $this->assertArrayHasKey('description', $formatted);
        $this->assertArrayHasKey('suggestion', $formatted);
        $this->assertEquals('Additional context', $formatted['additional_info']);
    }

    public function test_extracts_message_description_suggestion()
    {
        $this->assertEquals('Bad Request', MuseWalletErrorCodes::message(MuseWalletErrorCodes::BAD_REQUEST));
        $this->assertIsString(MuseWalletErrorCodes::description(MuseWalletErrorCodes::BAD_REQUEST));
        $this->assertIsString(MuseWalletErrorCodes::suggestion(MuseWalletErrorCodes::BAD_REQUEST));
    }
}

