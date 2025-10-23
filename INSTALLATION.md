# MuseWallet SDK - Installation & Integration Guide

## Quick Start

### 1. Installation

```bash
# Install the package
composer require artempuzik/musewallet-sdk-laravel
```

**That's it!** The package is ready to use immediately. Laravel will automatically discover and register the service provider.

### 2. Configuration

Add required environment variables to your `.env`:

```env
MUSEWALLET_API_URL=https://api.test.musepay.io
MUSEWALLET_PARTNER_ID=your_partner_id
MUSEWALLET_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
Your private key here
-----END RSA PRIVATE KEY-----"

# Card Products (get from MusePay)
MUSEWALLET_BASIC_CARD_PRODUCT_ID=prod_xxx
MUSEWALLET_PREMIUM_CARD_PRODUCT_ID=prod_yyy

# Webhook Secret
MUSEWALLET_WEBHOOK_SECRET=your_secret
```

### 3. Basic Usage

```php
use MuseWallet\SDK\Facades\MuseWallet;

// Get balance
$balance = MuseWallet::getPartnerBalance('USDT');

// Create card holder
$holder = MuseWallet::createCardHolder([
    'user_xid' => 'user_123',
    'email' => 'user@example.com',
    'individual' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birthday' => '1990-01-01'
    ]
]);
```

### 4. Advanced Configuration (Optional)

The package works out-of-the-box with environment variables. If you need to customize advanced settings, publish the configuration file:

```bash
php artisan vendor:publish --provider="MuseWallet\SDK\MuseWalletServiceProvider" --tag=musewallet-config
```

This creates `config/musewallet.php` where you can customize:
- API timeouts and retry attempts
- Cache settings (TTL, prefix)
- Logging configuration
- Event dispatching
- Multiple card product definitions

> **Note:** Publishing config is **optional**. Only do this if you need to override default behavior or manage complex card product configurations.

## Integration with Existing Laravel Project

### Step 1: Replace Imports

**Old:**
```php
use App\Services\MuseWallet\MuseWalletService;
use App\Services\MuseWallet\Enums\CardStatus;
```

**New:**
```php
use MuseWallet\SDK\Services\MuseWalletService;
use MuseWallet\SDK\Enums\CardStatus;
```

### Step 2: Update Controllers

**Before:**
```php
class MuseWalletController extends Controller
{
    public function __construct(
        private \App\Services\MuseWallet\MuseWalletService $service
    ) {}
}
```

**After:**
```php
use MuseWallet\SDK\Services\MuseWalletService;

class MuseWalletController extends Controller
{
    public function __construct(
        private MuseWalletService $service
    ) {}
}
```

### Step 3: Setup Event Listeners

Create listeners for webhook events:

```php
// app/Listeners/HandleCardCreated.php
namespace App\Listeners;

use MuseWallet\SDK\Events\CardCreatedEvent;

class HandleCardCreated
{
    public function handle(CardCreatedEvent $event)
    {
        $cardId = $event->getCardId();
        $userId = $event->getUserId();

        // Your logic here
    }
}
```

Register in `EventServiceProvider`:

```php
use MuseWallet\SDK\Events\{
    CardCreatedEvent,
    TransactionCompletedEvent,
    KycApprovedEvent
};

protected $listen = [
    CardCreatedEvent::class => [
        HandleCardCreated::class,
    ],
    TransactionCompletedEvent::class => [
        UpdateTransactionHistory::class,
        SendTransactionReceipt::class,
    ],
    KycApprovedEvent::class => [
        EnableCardFeatures::class,
    ],
];
```

### Step 4: Setup Webhook Route

```php
// routes/api.php
use MuseWallet\SDK\Services\MuseWalletService;

Route::post('/webhooks/musewallet', function (
    Request $request,
    MuseWalletService $service
) {
    $result = $service->processWebhook(
        $request->all(),
        $request->header('X-Webhook-Signature', '')
    );

    return response()->json($result);
});
```

### Step 5: Remove Old Files

After successful migration, remove:
```
app/Services/MuseWallet/
app/Http/Requests/MuseWallet/
app/Providers/MuseWalletServiceProvider.php (old one)
config/musewallet.php (old one, use published one)
```

## Event Handling Examples

### Transaction Notification

```php
namespace App\Listeners;

use MuseWallet\SDK\Events\TransactionCompletedEvent;
use App\Notifications\TransactionCompleted;

class SendTransactionNotification
{
    public function handle(TransactionCompletedEvent $event)
    {
        $user = User::whereHas('cards', function($q) use ($event) {
            $q->where('card_id', $event->getCardId());
        })->first();

        if ($user) {
            $user->notify(new TransactionCompleted([
                'amount' => $event->getAmount(),
                'currency' => $event->getCurrency(),
                'merchant' => $event->getMerchantName(),
            ]));
        }
    }
}
```

### KYC Status Update

```php
namespace App\Listeners;

use MuseWallet\SDK\Events\KycApprovedEvent;

class UpdateKycStatus
{
    public function handle(KycApprovedEvent $event)
    {
        $user = User::where('musewallet_user_id', $event->getUserId())
            ->first();

        if ($user) {
            $user->update([
                'kyc_status' => 'approved',
                'kyc_level' => $event->getKycLevel(),
                'kyc_approved_at' => now(),
            ]);

            // Enable features that require KYC
            $user->activatePremiumFeatures();
        }
    }
}
```

### Card Activation

```php
namespace App\Listeners;

use MuseWallet\SDK\Events\CardActivatedEvent;

class HandleCardActivation
{
    public function handle(CardActivatedEvent $event)
    {
        Card::where('card_id', $event->getCardId())
            ->update([
                'status' => 'active',
                'activated_at' => $event->getActivatedAt(),
            ]);

        // Send email to user
        $card = Card::where('card_id', $event->getCardId())->first();
        Mail::to($card->user)->send(new CardActivated($card));
    }
}
```

## Testing

### Unit Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/Unit/EnumTest.php

# With coverage
./vendor/bin/phpunit --coverage-html coverage
```

### Testing in Your Application

```php
use MuseWallet\SDK\Facades\MuseWallet;
use Illuminate\Support\Facades\Http;

public function test_can_create_card_holder()
{
    Http::fake([
        'api.test.musepay.io/*' => Http::response([
            'code' => '200',
            'data' => ['user_id' => 'user_123']
        ])
    ]);

    $result = MuseWallet::createCardHolder([
        'user_xid' => 'test_user',
        'email' => 'test@example.com'
    ]);

    $this->assertEquals('user_123', $result['user_id']);
}
```

## Troubleshooting

### Common Issues

#### 1. Private Key Error

```
Error: Invalid private key provided
```

**Solution:** Ensure your private key is properly formatted in `.env`:
```env
MUSEWALLET_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC...
...
-----END RSA PRIVATE KEY-----"
```

#### 2. Signature Error (406)

```
Error: Signature Error - The request signature is incorrect
```

**Solutions:**
- Verify private key matches your partner account
- Check partner_id is correct
- Ensure all required fields are included
- Verify timestamp is current

#### 3. Events Not Dispatching

**Check:**
```php
// config/musewallet.php
'events' => [
    'enabled' => true,  // Must be true
    'dispatch_on_webhook' => true,  // Must be true
],
```

#### 4. Webhook Signature Verification Fails

**Temporary workaround for testing:**
```php
// .env
MUSEWALLET_TESTING_MODE=true  // Disables signature verification
```

**Production solution:**
- Verify webhook secret matches MusePay configuration
- Check signature is sent in correct header

## Configuration Options

### Cache Configuration

```php
'cache' => [
    'enabled' => true,
    'ttl' => 300,  // 5 minutes
    'prefix' => 'musewallet:',
],
```

### Logging Configuration

```php
'logging' => [
    'enabled' => true,
    'level' => 'info',  // debug, info, warning, error
    'channel' => 'stack',
],
```

### Retry Configuration

```php
'api' => [
    'timeout' => 30,
    'retry_attempts' => 3,  // Number of retries
],
```

## Production Checklist

- [ ] Configure correct API URL (production vs test)
- [ ] Set partner_id and private_key from MusePay
- [ ] Configure card product IDs
- [ ] Set webhook secret
- [ ] Setup webhook endpoint
- [ ] Register event listeners
- [ ] Test webhook signature verification
- [ ] Enable logging
- [ ] Configure cache if needed
- [ ] Setup monitoring for failed webhooks
- [ ] Test retry logic
- [ ] Document KYC requirements for users

## Support & Resources

- **Documentation:** See README.md
- **Changelog:** See CHANGELOG.md
- **Issues:** GitHub Issues (after publication)
- **MusePay Docs:** https://docs-card.musepay.io

## Next Steps

1. ✅ Install package
2. ✅ Configure environment
3. ✅ Update imports
4. ✅ Setup event listeners
5. ✅ Test integration
6. ✅ Deploy to production
7. 📊 Monitor and optimize

---

For detailed API reference, see README.md

