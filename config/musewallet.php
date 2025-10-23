<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MuseWallet API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MuseWallet payment card API integration
    |
    */

    'api' => [
        'base_url' => env('MUSEWALLET_API_URL', 'https://api.test.musepay.io'),
        'partner_id' => env('MUSEWALLET_PARTNER_ID'),
        'private_key' => env('MUSEWALLET_PRIVATE_KEY'),
        'timeout' => env('MUSEWALLET_API_TIMEOUT', 30),
        'retry_attempts' => env('MUSEWALLET_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Agent Configuration
    |--------------------------------------------------------------------------
    |
    | Agent account settings for MuseWallet
    |
    */

    'agent' => [
        'account_id' => env('MUSEWALLET_AGENT_ACCOUNT_ID'),
        'default_currency' => env('MUSEWALLET_DEFAULT_CURRENCY', 'USD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Card Products Configuration
    |--------------------------------------------------------------------------
    |
    | MusePay API does not provide an endpoint to retrieve available card products.
    | You must configure your card products here with the product IDs provided by MusePay.
    | Contact MusePay support to get your available product IDs.
    |
    | Example product IDs (replace with your actual product IDs from MusePay):
    | - 'basic' => 'prod_basic_card_123'
    | - 'premium' => 'prod_premium_card_456'
    |
    */
    'card_products' => [
        'basic' => env('MUSEWALLET_BASIC_CARD_PRODUCT_ID'),
        'premium' => env('MUSEWALLET_PREMIUM_CARD_PRODUCT_ID'),
        'business' => env('MUSEWALLET_BUSINESS_CARD_PRODUCT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Webhook settings for transaction notifications
    |
    */

    'webhooks' => [
        'secret' => env('MUSEWALLET_WEBHOOK_SECRET'),
        'url' => env('MUSEWALLET_WEBHOOK_URL', '/api/v1/musewallet/webhook'),
        'events' => [
            'card.created',
            'card.activated',
            'card.blocked',
            'transaction.completed',
            'transaction.failed',
            'topup.completed',
            'kyc.approved',
            'kyc.rejected',
            'application.approved',
            'application.rejected',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for API responses
    |
    */

    'cache' => [
        'enabled' => env('MUSEWALLET_CACHE_ENABLED', true),
        'ttl' => env('MUSEWALLET_CACHE_TTL', 300), // 5 minutes
        'prefix' => 'musewallet:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging settings for MuseWallet service
    |
    */

    'logging' => [
        'enabled' => env('MUSEWALLET_LOGGING_ENABLED', true),
        'level' => env('MUSEWALLET_LOG_LEVEL', 'info'),
        'channel' => env('MUSEWALLET_LOG_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Test mode settings
    |
    */

    'testing' => [
        'enabled' => env('MUSEWALLET_TESTING_MODE', false),
        'mock_responses' => env('MUSEWALLET_MOCK_RESPONSES', false),
        'test_webhook_url' => env('MUSEWALLET_TEST_WEBHOOK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Events Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which events should be dispatched
    |
    */

    'events' => [
        'enabled' => env('MUSEWALLET_EVENTS_ENABLED', true),
        'dispatch_on_webhook' => env('MUSEWALLET_DISPATCH_ON_WEBHOOK', true),
    ],
];

