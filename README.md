# MuseWallet SDK for Laravel

[![Latest Version](https://img.shields.io/packagist/v/artempuzik/musewallet-sdk-laravel.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/artempuzik/musewallet-sdk-laravel.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk-laravel)
[![License](https://img.shields.io/packagist/l/artempuzik/musewallet-sdk-laravel.svg)](https://packagist.org/packages/artempuzik/musewallet-sdk-laravel)

A comprehensive Laravel SDK for integrating with MusePay Card API (MuseWallet). This package provides a clean, event-driven interface for managing virtual and physical cards, KYC verification, transactions, and webhooks.

**API Version**: v1
**SDK Version**: 1.0.0
**Compatible with**: MusePay Card API v1
**Documentation**: https://docs-card.musepay.io

## Features

- 🚀 Easy integration with MusePay Card API v1
- 🎯 Full support for all MusePay endpoints
- 🔐 Built-in RSA signature generation and webhook verification
- 🎪 Event-driven webhook handling
- 📦 Comprehensive error codes with descriptions
- ✅ Extensive test coverage
- 📝 Type hints and PHPDoc annotations
- 🎁 **Typed responses (DTO)** for all API methods
- 🎮 **Ready-to-use controller** with validation
- 🔄 Automatic retry logic with exponential backoff
- 💾 Optional response caching
- 📊 Detailed logging support

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or 11.0
- OpenSSL PHP extension

## Installation

### Option 1: Install from Packagist (Recommended when published)

```bash
composer require artempuzik/musewallet-sdk-laravel
```

### Option 2: Install from GitHub (Before Packagist publication)

If the package is not yet published on Packagist, add the GitHub repository to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/artempuzik/musewallet-sdk-laravel.git"
    }
],
```

Then install:

```bash
composer require artempuzik/musewallet-sdk-laravel:^1.0
```

**That's it!** The package is ready to use. Laravel will automatically discover and register the service provider.

## Configuration

### Step 1: Environment Variables (Required)

Add the following environment variables to your `.env` file:

```env
MUSEWALLET_API_URL=https://api.test.musepay.io
MUSEWALLET_PARTNER_ID=your_partner_id
MUSEWALLET_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
Your private key here
-----END RSA PRIVATE KEY-----"

# Card Products (obtain from MusePay)
MUSEWALLET_BASIC_CARD_PRODUCT_ID=prod_basic_card_id
MUSEWALLET_PREMIUM_CARD_PRODUCT_ID=prod_premium_card_id

# Webhook Configuration
MUSEWALLET_WEBHOOK_SECRET=your_webhook_secret

# Optional Settings
MUSEWALLET_API_TIMEOUT=30
MUSEWALLET_RETRY_ATTEMPTS=3
MUSEWALLET_CACHE_ENABLED=true
MUSEWALLET_CACHE_TTL=300
MUSEWALLET_LOGGING_ENABLED=true
MUSEWALLET_EVENTS_ENABLED=true
```

### Step 2: Publish Configuration (Optional)

If you need to customize advanced settings (timeouts, cache, logging, card products), publish the configuration file:

```bash
php artisan vendor:publish --provider="MuseWallet\SDK\MuseWalletServiceProvider" --tag=musewallet-config
```

This will create `config/musewallet.php` where you can override default settings.

> **Note:** Publishing the config file is **optional**. The package works with environment variables alone. Only publish if you need to customize advanced settings or manage multiple card products.

## Basic Usage

### Using the Facade (with typed responses)

```php
use MuseWallet\SDK\Facades\MuseWallet;

// Get partner balance - returns PartnerBalanceResponse
$balance = MuseWallet::getPartnerBalance('USDT');
echo "Available: " . $balance->getAvailableBalanceFloat(); // Type-safe!
echo "Currency: " . $balance->currency;
echo "Freeze: " . $balance->freezeBalance;

// Create card holder - returns CardHolderResponse
$holder = MuseWallet::createCardHolder([
    'user_xid' => 'unique_user_id',
    'email' => 'user@example.com',
    'individual' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birthday' => '1990-01-01'
    ]
]);

// Typed properties with IDE autocomplete
$userId = $holder->getUserId();        // string|null
$kycStatus = $holder->getKycStatus();  // string|null
$email = $holder->email;               // string|null

// Apply for a card - returns CardApplicationResponse
$application = MuseWallet::applyCard([
    'user_id' => $holder->userId,  // Use typed property
    'request_id' => \Illuminate\Support\Str::uuid(),
    'card_product_id' => 'prod_basic_card_id',
    'card_level' => '1',
    'phone_number' => '1234567890',
    'phone_area_code' => '1'
]);

// Check application status
if ($application->isSuccessful()) {
    $applyId = $application->getApplyId();
    $status = $application->getStatus();
}
```

### Using Dependency Injection (with typed responses)

```php
use MuseWallet\SDK\Services\MuseWalletService;
use MuseWallet\SDK\DataTransferObjects\CardApplicationResponse;

class CardController extends Controller
{
    public function __construct(
        private MuseWalletService $museWallet
    ) {}

    public function createCard(Request $request): JsonResponse
    {
        // Returns typed CardApplicationResponse
        $application = $this->museWallet->applyCard($request->validated());

        // IDE autocomplete and type safety
        return response()->json([
            'success' => $application->isSuccessful(),
            'apply_id' => $application->getApplyId(),
            'status' => $application->getStatus(),
            'card_id' => $application->getCardId(),
        ]);
    }
}
```

## Available Methods

### Balance Operations

```php
// Get partner balance
$balance = MuseWallet::getPartnerBalance('USDT');
```

### Card Holder Management

```php
// Create card holder
$holder = MuseWallet::createCardHolder([
    'user_xid' => 'unique_user_id',
    'email' => 'user@example.com',
    'individual' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birthday' => '1990-01-01'
    ],
    'address' => [
        'country' => 'US',
        'state' => 'CA',
        'city' => 'Los Angeles',
        'postal_code' => '90001',
        'address_line1' => '123 Main St'
    ]
]);
```

### KYC Management

```php
// Upload KYC documents
$kyc = MuseWallet::uploadKyc([
    'user_xid' => 'unique_user_id',
    'individual' => [...],
    'document' => [
        'type' => '1', // 1=Passport, 2=ID Card
        'number' => 'P123456789',
        'front_image' => 'base64_encoded_image',
        'back_image' => 'base64_encoded_image'
    ]
]);

// Generate KYC link
$kycLink = MuseWallet::generateKycLink('unique_user_id');
```

### Card Operations

```php
// Apply for card
$application = MuseWallet::applyCard([...]);

// Query application result
$result = MuseWallet::queryApplyResult($requestId, $userId);

// Get card information
$card = MuseWallet::getCard($cardId, $userId);

// Activate card
$activated = MuseWallet::activateCard([
    'user_id' => $userId,
    'card_id' => $cardId
]);

// Top up card
$topup = MuseWallet::topUpCard([
    'request_id' => \Illuminate\Support\Str::uuid(),
    'card_id' => $cardId,
    'user_id' => $userId,
    'amount' => '100.00',
    'currency' => 'USDT'
]);
```

## Webhook Handling

### Setup Webhook Route

In your `routes/api.php`:

```php
use MuseWallet\SDK\Services\MuseWalletService;

Route::post('/webhooks/musewallet', function (Request $request, MuseWalletService $museWallet) {
    $payload = $request->all();
    $signature = $request->header('X-Webhook-Signature', '');

    try {
        $result = $museWallet->processWebhook($payload, $signature);
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
});
```

### Listening to Webhook Events

The SDK automatically dispatches events for all webhook types. You can listen to these events in your application:

```php
use MuseWallet\SDK\Events\{
    CardCreatedEvent,
    CardActivatedEvent,
    TransactionCompletedEvent,
    TopUpCompletedEvent,
    KycApprovedEvent,
    ApplicationApprovedEvent
};

// In your EventServiceProvider
protected $listen = [
    CardCreatedEvent::class => [
        SendCardCreatedNotification::class,
    ],
    TransactionCompletedEvent::class => [
        UpdateTransactionHistory::class,
        SendTransactionReceipt::class,
    ],
    TopUpCompletedEvent::class => [
        UpdateCardBalance::class,
    ],
];
```

### Available Events

- `CardCreatedEvent` - Card has been created
- `CardActivatedEvent` - Card has been activated
- `CardBlockedEvent` - Card has been blocked
- `TransactionCompletedEvent` - Transaction completed successfully
- `TransactionFailedEvent` - Transaction failed
- `TopUpCompletedEvent` - Card top-up completed
- `KycApprovedEvent` - KYC verification approved
- `KycRejectedEvent` - KYC verification rejected
- `ApplicationApprovedEvent` - Card application approved
- `ApplicationRejectedEvent` - Card application rejected

### Event Usage Example

```php
namespace App\Listeners;

use MuseWallet\SDK\Events\TransactionCompletedEvent;
use App\Notifications\TransactionCompleted;

class SendTransactionNotification
{
    public function handle(TransactionCompletedEvent $event)
    {
        $transactionId = $event->getTransactionId();
        $amount = $event->getAmount();
        $currency = $event->getCurrency();
        $merchantName = $event->getMerchantName();

        // Send notification to user
        $user = User::where('card_id', $event->getCardId())->first();
        $user->notify(new TransactionCompleted(
            $transactionId,
            $amount,
            $currency,
            $merchantName
        ));
    }
}
```

## Typed Responses (DTO)

All service methods return typed Data Transfer Objects for better IDE support and type safety:

### Available Response Types

| Response Class | Used By | Properties |
|----------------|---------|------------|
| `PartnerBalanceResponse` | `getPartnerBalance()` | currency, balance, availableBalance, freezeBalance |
| `CardHolderResponse` | `createCardHolder()`, `uploadKyc()` | userId, userXid, kycStatus, email, firstName, lastName |
| `CardApplicationResponse` | `applyCard()`, `queryApplyResult()` | applyId, requestId, userId, status, cardId |
| `CardInfoResponse` | `getCard()`, `activateCard()` | cardId, cardNumber, status, balance, expiryMonth/Year |
| `TopUpResponse` | `topUpCard()` | requestId, cardId, amount, currency, orderNo |
| `KycLinkResponse` | `generateKycLink()` | userXid, link |

### Usage Examples

```php
use MuseWallet\SDK\Facades\MuseWallet;

// Get balance with typed response
$balance = MuseWallet::getPartnerBalance('USDT');

// Access properties with autocomplete
$available = $balance->availableBalance;           // string|null
$availableFloat = $balance->getAvailableBalanceFloat();  // float|null
$isSuccess = $balance->isSuccessful();            // bool
$rawData = $balance->data;                        // mixed (full API response)

// Card application with typed response
$application = MuseWallet::applyCard([...]);

$applyId = $application->applyId;           // string|null
$status = $application->getStatus();        // string|null
$cardId = $application->getCardId();        // string|null

// Card info with typed response
$card = MuseWallet::getCard($cardId, $userId);

$masked = $card->getMaskedCardNumber();     // ****1234
$isActive = $card->isActive();              // bool
$balance = $card->getBalanceFloat();        // float|null

// KYC link with typed response
$kycLink = MuseWallet::generateKycLink($userXid);

if ($kycLink->hasLink()) {
    $url = $kycLink->getLink();  // string|null
}

// Top-up with typed response
$topup = MuseWallet::topUpCard([...]);

$amount = $topup->getAmountFloat();         // float|null
$isSuccess = $topup->isTopUpSuccessful();   // bool
```

### Benefits of Typed Responses

✅ **IDE Autocomplete** - Full IntelliSense support
✅ **Type Safety** - Catch errors at development time
✅ **Documentation** - Self-documenting code
✅ **Helper Methods** - Convenient getters and checks
✅ **Raw Data Access** - Full API response always available

## Enums

The SDK provides several enum classes for type safety:

```php
use MuseWallet\SDK\Enums\{
    ApplyStatus,
    CardStatus,
    CardLevel,
    KycStatus,
    Currency,
    DocumentType
};

// Check application status
if (ApplyStatus::isSuccessful($status)) {
    // Handle successful application
}

// Check if currency is supported
if (Currency::isSupported('USDT')) {
    // Process payment
}

// Get status label
$label = KycStatus::label(KycStatus::APPROVED); // "Approved"
```

## Error Handling

The SDK includes comprehensive error codes with descriptions:

```php
use MuseWallet\SDK\Services\MuseWalletErrorCodes;

try {
    $result = MuseWallet::applyCard($data);
} catch (\MuseWallet\SDK\Exceptions\MuseWalletException $e) {
    $code = $e->getCode();

    // Check if error is retryable
    if (MuseWalletErrorCodes::isRetryable($code)) {
        // Retry the operation
    }

    // Check if user action is required
    if (MuseWalletErrorCodes::requiresUserAction($code)) {
        // Notify user
    }

    // Get detailed error information
    $errorInfo = MuseWalletErrorCodes::get($code);
    // Returns: ['message' => '...', 'description' => '...', 'suggestion' => '...']
}
```

## Testing

The SDK includes comprehensive tests:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run static analysis
composer analyse
```

## Card Products Configuration

MusePay API does not provide an endpoint to retrieve card products. You must configure your product IDs in the config file or environment variables after receiving them from MusePay support.

```php
// config/musewallet.php
'card_products' => [
    'basic' => env('MUSEWALLET_BASIC_CARD_PRODUCT_ID'),
    'premium' => env('MUSEWALLET_PREMIUM_CARD_PRODUCT_ID'),
    'business' => env('MUSEWALLET_BUSINESS_CARD_PRODUCT_ID'),
],
```

## Caching

The SDK supports response caching for improved performance:

```php
// Enable caching in config/musewallet.php
'cache' => [
    'enabled' => true,
    'ttl' => 300, // 5 minutes
    'prefix' => 'musewallet:',
],
```

## Logging

Detailed logging can be enabled for debugging:

```php
// config/musewallet.php
'logging' => [
    'enabled' => true,
    'level' => 'info',
    'channel' => 'stack',
],
```

## Security

- All API requests are signed with RSA SHA1 signatures
- Webhook signatures are verified before processing
- Private keys should be stored securely in environment variables
- Never commit private keys to version control

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues, questions, or contributions, please use the GitHub issue tracker.

## Credits

- [Artem Puzik](https://github.com/artempuzik)
- Based on MusePay Card API documentation

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

