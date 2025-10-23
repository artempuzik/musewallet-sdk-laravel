# MuseWallet SDK - Controller Usage Guide

## Обзор

SDK предоставляет готовый контроллер `MuseWallet\SDK\Http\Controllers\MuseWalletController` со всеми необходимыми методами. Вы можете использовать его тремя способами:

1. **Напрямую** - использовать контроллер SDK в роутах
2. **Через наследование** - расширить контроллер для кастомизации
3. **Через сервис** - использовать только сервис напрямую

---

## Способ 1: Использование контроллера напрямую (Рекомендуется)

Самый простой способ - использовать контроллер SDK напрямую в ваших роутах.

### Регистрация роутов

```php
// routes/api.php
use MuseWallet\SDK\Http\Controllers\MuseWalletController;

Route::group(['prefix' => 'musewallet'], function () {
    // Webhook endpoint
    Route::post('/webhook', [MuseWalletController::class, 'webhook']);

    // Public endpoints
    Route::get('/card-products', [MuseWalletController::class, 'getCardProducts']);

    // Protected endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/balance', [MuseWalletController::class, 'getPartnerBalance']);
        Route::post('/card-holders', [MuseWalletController::class, 'createCardHolder']);
        Route::post('/card/apply', [MuseWalletController::class, 'applyCard']);
        Route::post('/card/query', [MuseWalletController::class, 'getCardInfo']);
        Route::post('/card/activate', [MuseWalletController::class, 'activateCard']);
        Route::post('/card/topup', [MuseWalletController::class, 'topUpCard']);
        Route::post('/carduser/upload-kyc', [MuseWalletController::class, 'uploadKyc']);
        Route::post('/carduser/kyc-link', [MuseWalletController::class, 'generateKycLink']);
        Route::post('/card/apply-result', [MuseWalletController::class, 'queryApplyResult']);
    });
});
```

### Преимущества
- ✅ Минимальный код
- ✅ Готовая валидация
- ✅ Готовая обработка ошибок
- ✅ Автоматические события

---

## Способ 2: Наследование и кастомизация (Гибкость)

Создайте свой контроллер, наследуясь от базового SDK контроллера.

### Создание контроллера

```php
// app/Http/Controllers/Api/V1/MuseWalletController.php
namespace App\Http\Controllers\Api\V1;

use MuseWallet\SDK\Http\Controllers\MuseWalletController as BaseMuseWalletController;
use MuseWallet\SDK\Http\Requests\CreateCardHolderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MuseWalletController extends BaseMuseWalletController
{
    /**
     * Override webhook to add custom logging
     */
    public function webhook(Request $request): JsonResponse
    {
        Log::info('webhook received');

        // Call parent's webhook handler
        $result = parent::webhook($request);

        // Custom post-processing
        // e.g., trigger additional events, update metrics

        return $result;
    }

    /**
     * Override createCardHolder to save locally
     */
    public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
    {
        // Call SDK's method (with validation and API call)
        $result = parent::createCardHolder($request);

        // If successful, save to local database
        if ($result->getData()->success) {
            $userData = $result->getData()->data;

            \App\Models\User::where('id', auth()->id())->update([
                'musewallet_user_id' => $userData->user_id,
                'musewallet_user_xid' => $request->input('user_xid'),
            ]);
        }

        return $result;
    }

    /**
     * Add custom method
     */
    public function getMyCards(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->musewallet_user_id) {
            return response()->json([
                'success' => false,
                'error' => 'User has no MuseWallet account'
            ], 404);
        }

        // Use service directly
        try {
            $cards = $this->museWalletService->getCard(
                $user->musewallet_card_id,
                $user->musewallet_user_id
            );

            return response()->json([
                'success' => true,
                'data' => $cards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
```

### Регистрация роутов

```php
// routes/api.php
use App\Http\Controllers\Api\V1\MuseWalletController;

Route::group(['prefix' => 'musewallet'], function () {
    Route::post('/webhook', [MuseWalletController::class, 'webhook']);

    Route::middleware(['auth:sanctum'])->group(function () {
        // SDK methods (inherited)
        Route::post('/card-holders', [MuseWalletController::class, 'createCardHolder']);
        Route::post('/card/apply', [MuseWalletController::class, 'applyCard']);

        // Custom methods
        Route::get('/my-cards', [MuseWalletController::class, 'getMyCards']);
    });
});
```

### Преимущества
- ✅ Использование готовой логики SDK
- ✅ Возможность переопределения
- ✅ Добавление кастомных методов
- ✅ Гибкость в обработке

---

## Способ 3: Использование сервиса напрямую (Максимальный контроль)

Создайте полностью кастомный контроллер, используя только сервис.

### Создание контроллера

```php
// app/Http/Controllers/Api/V1/CardController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use MuseWallet\SDK\Services\MuseWalletService;
use MuseWallet\SDK\Facades\MuseWallet;
use MuseWallet\SDK\Enums\CardStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    public function __construct(
        private MuseWalletService $museWallet
    ) {}

    /**
     * Create card with full custom logic
     */
    public function createCard(Request $request): JsonResponse
    {
        // Custom validation
        $request->validate([
            'card_type' => 'required|in:basic,premium',
        ]);

        $user = auth()->user();

        // Step 1: Create card holder if needed
        if (!$user->musewallet_user_id) {
            $holder = $this->museWallet->createCardHolder([
                'user_xid' => 'user_' . $user->id,
                'email' => $user->email,
                'individual' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'birthday' => $user->date_of_birth
                ],
                'address' => [
                    'country' => $user->country,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                    'address_line1' => $user->address
                ]
            ]);

            // Save to database
            $user->update([
                'musewallet_user_id' => $holder['user_id']
            ]);
        }

        // Step 2: Apply for card
        $cardType = $request->input('card_type');
        $productId = config("musewallet.card_products.{$cardType}");

        $application = $this->museWallet->applyCard([
            'user_id' => $user->musewallet_user_id,
            'request_id' => \Illuminate\Support\Str::uuid(),
            'card_product_id' => $productId,
            'card_level' => '1',
            'phone_number' => $user->phone,
            'phone_area_code' => $user->phone_code
        ]);

        // Step 3: Save application to database
        \App\Models\CardApplication::create([
            'user_id' => $user->id,
            'request_id' => $application['request_id'],
            'apply_id' => $application['apply_id'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Card application submitted',
            'data' => $application
        ]);
    }

    /**
     * Or use Facade
     */
    public function quickBalance(): JsonResponse
    {
        $balance = MuseWallet::getPartnerBalance('USDT_TRC20');

        return response()->json($balance);
    }
}
```

### Преимущества
- ✅ Полный контроль над логикой
- ✅ Кастомная валидация
- ✅ Интеграция с вашими моделями
- ✅ Сложная бизнес-логика

---

## Сравнение подходов

| Аспект | Напрямую | Наследование | Сервис напрямую |
|--------|----------|--------------|-----------------|
| **Простота** | ⭐⭐⭐ | ⭐⭐ | ⭐ |
| **Гибкость** | ⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Код** | Минимум | Средне | Максимум |
| **Контроль** | Базовый | Высокий | Полный |
| **Валидация** | Готовая | Готовая + кастом | Полный кастом |
| **События** | Автоматически | Автоматически | Ручное управление |

---

## Примеры кастомизации

### 1. Добавление аутентификации перед созданием карты

```php
public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
{
    // Check if user already has a card holder
    if (auth()->user()->musewallet_user_id) {
        return response()->json([
            'success' => false,
            'error' => 'Card holder already exists'
        ], 400);
    }

    // Call parent
    $result = parent::createCardHolder($request);

    // Save to database
    if ($result->getData()->success) {
        auth()->user()->update([
            'musewallet_user_id' => $result->getData()->data->user_id
        ]);
    }

    return $result;
}
```

### 2. Добавление дополнительной валидации

```php
public function applyCard(ApplyCardRequest $request): JsonResponse
{
    $user = auth()->user();

    // Additional business logic validation
    if (!$user->kyc_verified) {
        return response()->json([
            'success' => false,
            'error' => 'KYC verification required before applying for card'
        ], 403);
    }

    if ($user->cards()->count() >= 5) {
        return response()->json([
            'success' => false,
            'error' => 'Maximum card limit reached'
        ], 403);
    }

    // Call parent SDK method
    return parent::applyCard($request);
}
```

### 3. Добавление логирования в БД

```php
public function topUpCard(TopUpCardRequest $request): JsonResponse
{
    $data = $request->validated();

    // Create pending transaction record
    $transaction = Transaction::create([
        'user_id' => auth()->id(),
        'type' => 'card_topup',
        'status' => 'pending',
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'request_id' => $data['request_id'],
    ]);

    // Call SDK method
    $result = parent::topUpCard($request);

    // Update transaction status
    if ($result->getData()->success) {
        $transaction->update(['status' => 'completed']);
    } else {
        $transaction->update(['status' => 'failed']);
    }

    return $result;
}
```

### 4. Интеграция с системой уведомлений

```php
public function activateCard(ActivateCardRequest $request): JsonResponse
{
    $result = parent::activateCard($request);

    if ($result->getData()->success) {
        $user = auth()->user();

        // Send notification
        $user->notify(new CardActivatedNotification(
            $result->getData()->data
        ));

        // Send email
        Mail::to($user)->send(new CardActivated($user));
    }

    return $result;
}
```

---

## Использование сервиса напрямую

Если вам нужна полностью кастомная логика, используйте сервис:

### Через Dependency Injection

```php
use MuseWallet\SDK\Services\MuseWalletService;

class MyController extends Controller
{
    public function __construct(
        private MuseWalletService $museWallet
    ) {}

    public function complexOperation(Request $request)
    {
        // Step 1: Create holder
        $holder = $this->museWallet->createCardHolder([...]);

        // Step 2: Upload KYC
        $kyc = $this->museWallet->uploadKyc([...]);

        // Step 3: Apply for card
        $card = $this->museWallet->applyCard([...]);

        // Custom logic between steps
        // ...

        return response()->json([...]);
    }
}
```

### Через Facade

```php
use MuseWallet\SDK\Facades\MuseWallet;

class QuickController extends Controller
{
    public function quickBalance()
    {
        $balance = MuseWallet::getPartnerBalance('USDT_TRC20');
        return response()->json($balance);
    }

    public function quickTopUp(Request $request)
    {
        $result = MuseWallet::topUpCard([
            'request_id' => \Str::uuid(),
            'card_id' => $request->card_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);

        return response()->json($result);
    }
}
```

---

## Доступные методы в контроллере

### Публичные методы

| Метод | Описание | Request Class |
|-------|----------|---------------|
| `webhook()` | Обработка вебхуков | - |
| `getCardProducts()` | Список продуктов карт | - |

### Защищенные методы (требуют auth)

| Метод | Описание | Request Class |
|-------|----------|---------------|
| `getPartnerBalance()` | Баланс партнера | - |
| `createCardHolder()` | Создать держателя | CreateCardHolderRequest |
| `applyCard()` | Заявка на карту | ApplyCardRequest |
| `queryApplyResult()` | Статус заявки | QueryApplyResultRequest |
| `getCardInfo()` | Информация о карте | GetCardInfoRequest |
| `activateCard()` | Активация карты | ActivateCardRequest |
| `topUpCard()` | Пополнение карты | TopUpCardRequest |
| `uploadKyc()` | Загрузка KYC | UploadKycRequest |
| `generateKycLink()` | Генерация KYC ссылки | GenerateKycLinkRequest |

---

## Валидация

Все Request классы включают:
- ✅ Валидацию обязательных полей
- ✅ Валидацию через Enum'ы
- ✅ Кастомные сообщения об ошибках
- ✅ Вложенную валидацию для объектов

### Пример валидации

```php
// POST /api/musewallet/card/apply
{
    "user_id": "user_123",
    "card_level": "999"  // Invalid!
}

// Response (422):
{
    "message": "The given data was invalid.",
    "errors": {
        "request_id": ["Request ID is required (use UUID)"],
        "card_product_id": ["Card product ID is required"],
        "card_level": ["The selected card level is invalid."],
        "phone_number": ["Phone number is required"],
        "phone_area_code": ["Phone area code is required"]
    }
}
```

---

## Обработка ошибок

Контроллер автоматически обрабатывает:
- `MuseWalletException` → 400 Bad Request
- Validation errors → 422 Unprocessable Entity
- Generic exceptions → 500 Internal Server Error

### Кастомная обработка ошибок

```php
public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
{
    try {
        $result = parent::createCardHolder($request);

        return $result;

    } catch (\MuseWallet\SDK\Exceptions\MuseWalletException $e) {
        // Custom error handling
        Log::critical('MuseWallet error', [
            'error' => $e->getMessage(),
            'context' => $e->getContext(),
            'user_id' => auth()->id()
        ]);

        // Send alert to admin
        \App\Notifications\AdminAlert::send('MuseWallet Error', $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => 'An error occurred. Support team has been notified.'
        ], 400);
    }
}
```

---

## События

Контроллер автоматически dispatches события при обработке вебхуков:

```php
// Вебхук приходит -> SDK dispatches событие -> Ваш Listener обрабатывает

// EventServiceProvider.php
use MuseWallet\SDK\Events\TransactionCompletedEvent;

protected $listen = [
    TransactionCompletedEvent::class => [
        UpdateUserBalance::class,
        SendTransactionEmail::class,
    ],
];
```

События dispatches автоматически, если:
- `MUSEWALLET_EVENTS_ENABLED=true`
- `MUSEWALLET_DISPATCH_ON_WEBHOOK=true`

---

## Рекомендации

### Для простых проектов
Используйте контроллер SDK напрямую:
```php
Route::post('/webhook', [\MuseWallet\SDK\Http\Controllers\MuseWalletController::class, 'webhook']);
```

### Для средних проектов
Наследуйтесь и переопределяйте:
```php
class MuseWalletController extends \MuseWallet\SDK\Http\Controllers\MuseWalletController
{
    public function createCardHolder(...) {
        $result = parent::createCardHolder(...);
        // + ваша логика
        return $result;
    }
}
```

### Для сложных проектов
Используйте сервис напрямую:
```php
public function complexFlow(MuseWalletService $service) {
    $step1 = $service->createCardHolder([...]);
    // Ваша логика между шагами
    $step2 = $service->uploadKyc([...]);
    // Ваша логика
    $step3 = $service->applyCard([...]);
}
```

---

## Доступ к сервису в контроллере

Если вы наследуетесь от базового контроллера, у вас есть доступ к сервису:

```php
class MuseWalletController extends BaseMuseWalletController
{
    public function customMethod()
    {
        // Сервис доступен через $this->museWalletService
        $balance = $this->museWalletService->getPartnerBalance('USDT');

        // Или через Facade
        $balance = \MuseWallet\SDK\Facades\MuseWallet::getPartnerBalance('USDT');

        return response()->json($balance);
    }
}
```

---

## Middleware

Добавьте middleware для защиты эндпоинтов:

```php
// routes/api.php
Route::group([
    'prefix' => 'musewallet',
    'middleware' => ['auth:sanctum', 'verified']
], function () {
    Route::post('/card-holders', [MuseWalletController::class, 'createCardHolder']);
    Route::post('/card/apply', [MuseWalletController::class, 'applyCard']);
});

// Webhook без auth (но с проверкой подписи в SDK)
Route::post('/musewallet/webhook', [MuseWalletController::class, 'webhook']);
```

---

## Тестирование

### Тестирование базового контроллера

```php
use MuseWallet\SDK\Http\Controllers\MuseWalletController;
use Illuminate\Support\Facades\Http;

public function test_can_create_card_holder()
{
    Http::fake([
        'api.test.musepay.io/*' => Http::response([
            'code' => '200',
            'data' => ['user_id' => 'user_123']
        ])
    ]);

    $response = $this->postJson('/api/musewallet/card-holders', [
        'user_xid' => 'user_ext_123',
        'email' => 'test@example.com',
        'individual' => [...],
        'address' => [...]
    ]);

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
}
```

### Тестирование переопределенного контроллера

```php
public function test_saves_user_id_after_creating_holder()
{
    Http::fake([...]);

    $response = $this->actingAs($user)
        ->postJson('/api/musewallet/card-holders', [...]);

    $response->assertStatus(200);

    // Check custom logic
    $this->assertNotNull($user->fresh()->musewallet_user_id);
}
```

---

## Лучшие практики

### 1. Не дублируйте код
❌ **Плохо:**
```php
class MuseWalletController extends BaseMuseWalletController
{
    public function createCardHolder(...) {
        // Копирование всей логики из SDK
        try {
            $result = $this->museWalletService->createCardHolder(...);
            return response()->json([...]);
        } catch (...) {}
    }
}
```

✅ **Хорошо:**
```php
class MuseWalletController extends BaseMuseWalletController
{
    public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
    {
        $result = parent::createCardHolder($request);

        // Только ваша кастомная логика
        $this->saveToDatabase($result);

        return $result;
    }
}
```

### 2. Используйте события вместо переопределения

❌ **Плохо:** Переопределять webhook для обработки
```php
public function webhook(...) {
    $result = parent::webhook(...);
    $this->handleTransaction(...);
    return $result;
}
```

✅ **Хорошо:** Использовать события
```php
// EventServiceProvider
TransactionCompletedEvent::class => [
    HandleTransaction::class
]
```

### 3. Валидация через Request классы

✅ **Используйте готовые:**
```php
public function createCardHolder(CreateCardHolderRequest $request) {
    // Валидация уже прошла
    $data = $request->validated();
}
```

✅ **Расширяйте при необходимости:**
```php
class MyCreateCardHolderRequest extends CreateCardHolderRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'referral_code' => 'nullable|exists:referrals,code'
        ]);
    }
}
```

---

## Маршруты

Пример полной настройки роутов в приложении:

```php
// routes/api.php

use MuseWallet\SDK\Http\Controllers\MuseWalletController;
// или
use App\Http\Controllers\Api\V1\MuseWalletController; // если расширили

Route::group(['prefix' => 'v1'], function () {

    // Webhook (публичный, но защищен проверкой подписи в SDK)
    Route::post('/musewallet/webhook', [MuseWalletController::class, 'webhook'])
        ->name('musewallet.webhook');

    // Публичные эндпоинты
    Route::get('/musewallet/card-products', [MuseWalletController::class, 'getCardProducts'])
        ->name('musewallet.card-products');

    // Защищенные эндпоинты
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('musewallet')->group(function () {

            // Balance
            Route::get('/balance', [MuseWalletController::class, 'getPartnerBalance'])
                ->name('musewallet.balance');

            // Card Holder
            Route::post('/card-holders', [MuseWalletController::class, 'createCardHolder'])
                ->name('musewallet.create-card-holder');

            // KYC
            Route::post('/carduser/upload-kyc', [MuseWalletController::class, 'uploadKyc'])
                ->name('musewallet.upload-kyc');
            Route::post('/carduser/kyc-link', [MuseWalletController::class, 'generateKycLink'])
                ->name('musewallet.generate-kyc-link');

            // Card Operations
            Route::post('/card/apply', [MuseWalletController::class, 'applyCard'])
                ->name('musewallet.apply-card');
            Route::post('/card/apply-result', [MuseWalletController::class, 'queryApplyResult'])
                ->name('musewallet.query-apply-result');
            Route::post('/card/query', [MuseWalletController::class, 'getCardInfo'])
                ->name('musewallet.get-card');

            // Card Actions
            Route::post('/card/activate', [MuseWalletController::class, 'activateCard'])
                ->name('musewallet.activate-card');
            Route::post('/card/topup', [MuseWalletController::class, 'topUpCard'])
                ->name('musewallet.topup-card');
        });
    });
});
```

---

## Заключение

SDK предоставляет три уровня гибкости:

1. **Level 1 (Fastest)** - Используйте контроллер SDK напрямую
   - Для простых проектов
   - Минимум кода
   - Готово к использованию

2. **Level 2 (Recommended)** - Наследуйтесь и кастомизируйте
   - Для большинства проектов
   - Гибкость + готовая база
   - Легко расширять

3. **Level 3 (Advanced)** - Используйте сервис напрямую
   - Для сложных проектов
   - Полный контроль
   - Максимальная гибкость

Выбирайте подход в зависимости от ваших потребностей!

