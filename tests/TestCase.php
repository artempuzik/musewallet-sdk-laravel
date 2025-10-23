<?php

namespace MuseWallet\SDK\Tests;

use MuseWallet\SDK\MuseWalletServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Base TestCase for MuseWallet SDK tests
 */
abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration
        config([
            'musewallet.api.base_url' => 'https://api.test.musepay.io',
            'musewallet.api.partner_id' => 'test_partner_123',
            'musewallet.api.private_key' => $this->getTestPrivateKey(),
            'musewallet.api.timeout' => 30,
            'musewallet.api.retry_attempts' => 3,
            'musewallet.testing.enabled' => true,
            'musewallet.testing.mock_responses' => true,
            'musewallet.logging.enabled' => false,
            'musewallet.cache.enabled' => false,
            'musewallet.events.enabled' => true,
            'musewallet.events.dispatch_on_webhook' => true,
        ]);
    }

    /**
     * Get package providers
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MuseWalletServiceProvider::class,
        ];
    }

    /**
     * Get test private key for RSA signing
     *
     * @return string
     */
    protected function getTestPrivateKey(): string
    {
        // Generate a real test key on the fly for testing
        $config = [
            "digest_alg" => "sha1",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);

        return $privKey;
    }

    /**
     * Get environment setup
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

