<?php

namespace MuseWallet\SDK\Tests\Unit;

use MuseWallet\SDK\Services\SignatureParameters;
use MuseWallet\SDK\Tests\TestCase;

class SignatureParametersTest extends TestCase
{
    public function test_returns_fields_for_endpoint()
    {
        $fields = SignatureParameters::getFieldsForEndpoint('/v1/carduser/create');

        $this->assertIsArray($fields);
        $this->assertContains('partner_id', $fields);
        $this->assertContains('sign_type', $fields);
        $this->assertContains('timestamp', $fields);
        $this->assertContains('nonce', $fields);
        $this->assertContains('user_xid', $fields);
    }

    public function test_checks_if_field_should_be_included()
    {
        $this->assertTrue(
            SignatureParameters::shouldIncludeField('/v1/carduser/create', 'user_xid')
        );

        $this->assertTrue(
            SignatureParameters::shouldIncludeField('/v1/carduser/create', 'partner_id')
        );

        $this->assertFalse(
            SignatureParameters::shouldIncludeField('/v1/carduser/create', 'unknown_field')
        );
    }

    public function test_extracts_signature_parameters()
    {
        $data = [
            'partner_id' => 'test_partner',
            'sign_type' => 'RSA',
            'timestamp' => '1234567890',
            'nonce' => '1234567890',
            'user_xid' => 'user_123',
            'email' => 'test@example.com',
            'unknown_field' => 'should_not_be_included'
        ];

        $extracted = SignatureParameters::extract('/v1/carduser/create', $data);

        $this->assertIsArray($extracted);
        $this->assertArrayHasKey('partner_id', $extracted);
        $this->assertArrayHasKey('user_xid', $extracted);
        $this->assertArrayHasKey('email', $extracted);
        $this->assertArrayNotHasKey('unknown_field', $extracted);
    }

    public function test_handles_complex_objects_in_signature()
    {
        $data = [
            'partner_id' => 'test_partner',
            'sign_type' => 'RSA',
            'timestamp' => '1234567890',
            'nonce' => '1234567890',
            'user_xid' => 'user_123',
            'individual' => [
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]
        ];

        $extracted = SignatureParameters::extract('/v1/carduser/create', $data);

        $this->assertArrayHasKey('individual', $extracted);
        $this->assertIsString($extracted['individual']);
        $this->assertJson($extracted['individual']);
    }

    public function test_skips_empty_values()
    {
        $data = [
            'partner_id' => 'test_partner',
            'sign_type' => 'RSA',
            'timestamp' => '1234567890',
            'nonce' => '1234567890',
            'user_xid' => '',
            'email' => null,
        ];

        $extracted = SignatureParameters::extract('/v1/carduser/create', $data);

        $this->assertArrayNotHasKey('user_xid', $extracted);
        $this->assertArrayNotHasKey('email', $extracted);
    }

    public function test_sorts_parameters_alphabetically()
    {
        $data = [
            'timestamp' => '1234567890',
            'partner_id' => 'test_partner',
            'nonce' => '1234567890',
            'sign_type' => 'RSA',
        ];

        $extracted = SignatureParameters::extract('/v1/balance/partner', $data);

        $keys = array_keys($extracted);
        $sortedKeys = $keys;
        sort($sortedKeys);

        $this->assertEquals($sortedKeys, $keys);
    }
}

