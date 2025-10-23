<?php

namespace MuseWallet\SDK\Services;

use MuseWallet\SDK\Exceptions\MuseWalletException;
use MuseWallet\SDK\DataTransferObjects\{
    CardHolderResponse,
    CardApplicationResponse,
    PartnerBalanceResponse,
    CardInfoResponse,
    TopUpResponse,
    KycLinkResponse
};
use MuseWallet\SDK\Events\{
    CardCreatedEvent,
    CardActivatedEvent,
    CardBlockedEvent,
    TransactionCompletedEvent,
    TransactionFailedEvent,
    TopUpCompletedEvent,
    KycApprovedEvent,
    KycRejectedEvent,
    ApplicationApprovedEvent,
    ApplicationRejectedEvent
};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

/**
 * MuseWallet Service for MusePay API integration
 *
 * This service provides integration with MusePay Card API v1 for payment operations
 *
 * @link https://docs-card.musepay.io/reference/api-reference
 * @version 1.0.0
 * @api-version v1
 */
class MuseWalletService
{
    /**
     * MusePay Card API version
     */
    public const API_VERSION = 'v1';
    protected string $baseUrl;
    protected string $partnerId;
    protected string $privateKey;
    protected int $timeout;
    protected int $retryAttempts;
    protected bool $testingMode;
    protected bool $mockResponses;
    protected bool $eventsEnabled;

    public function __construct()
    {
        $this->baseUrl = config('musewallet.api.base_url', 'https://api.test.musepay.io');
        $this->partnerId = config('musewallet.api.partner_id', '');
        $this->privateKey = config('musewallet.api.private_key', '');
        $this->timeout = config('musewallet.api.timeout', 30);
        $this->retryAttempts = config('musewallet.api.retry_attempts', 3);
        $this->testingMode = config('musewallet.testing.enabled', false);
        $this->mockResponses = config('musewallet.testing.mock_responses', false);
        $this->eventsEnabled = config('musewallet.events.enabled', true);

        if (config('musewallet.logging.enabled')) {
            Log::channel(config('musewallet.logging.channel'))
                ->info('MuseWallet Service initialized', [
                    'base_url' => $this->baseUrl,
                    'testing_mode' => $this->testingMode,
                ]);
        }
    }

    /**
     * Get partner balance
     *
     * @param string $currency
     * @return PartnerBalanceResponse
     * @throws MuseWalletException
     */
    public function getPartnerBalance(string $currency = 'USDT'): PartnerBalanceResponse
    {
        $cacheKey = "musewallet:balance:{$currency}";

        if (config('musewallet.cache.enabled')) {
            $cached = Cache::get($cacheKey);
            if ($cached instanceof PartnerBalanceResponse) {
                return $cached;
            }
        }

        $response = $this->makeRequest('POST', '/v1/balance/partner', [
            'currency' => $currency
        ]);

        $balanceResponse = PartnerBalanceResponse::fromArray($response);

        if (config('musewallet.cache.enabled')) {
            Cache::put($cacheKey, $balanceResponse, config('musewallet.cache.ttl', 300));
        }

        return $balanceResponse;
    }

    /**
     * Get available card products from configuration
     *
     * Note: MusePay API does not provide an endpoint to retrieve card products.
     * Products must be configured in advance with MusePay and added to config/musewallet.php
     *
     * @return array
     */
    public function getCardProducts(): array
    {
        $products = config('musewallet.card_products', []);

        // Format products for API response
        $formattedProducts = [];
        foreach ($products as $key => $productId) {
            if (!empty($productId)) {
                $formattedProducts[] = [
                    'id' => $productId,
                    'name' => ucfirst($key) . ' Card',
                    'type' => $key,
                    'configured' => true
                ];
            }
        }

        return $formattedProducts;
    }

    /**
     * Create card holder
     *
     * @param array $holderData
     * @return CardHolderResponse
     * @throws MuseWalletException
     */
    public function createCardHolder(array $holderData): CardHolderResponse
    {
        $response = $this->makeRequest('POST', '/v1/carduser/create', $holderData);

        $this->logAction('create_card_holder', $holderData, $response);

        return CardHolderResponse::fromArray($response);
    }

    /**
     * Create card application
     *
     * @param array $applicationData
     * @return CardApplicationResponse
     * @throws MuseWalletException
     */
    public function applyCard(array $applicationData): CardApplicationResponse
    {
        $response = $this->makeRequest('POST', '/v1/card/apply', $applicationData);

        $this->logAction('create_card_application', $applicationData, $response);

        return CardApplicationResponse::fromArray($response);
    }

    /**
     * Query card application result
     * According to MusePay API: POST /v1/card/apply-result
     *
     * @param string $requestId External identifier for the issuing request
     * @param string $userId The unique id in musewallet
     * @param string|null $applyId The apply ID of the card issuing
     * @return CardApplicationResponse
     * @throws MuseWalletException
     */
    public function queryApplyResult(string $requestId, string $userId, ?string $applyId = null): CardApplicationResponse
    {
        $data = [
            'request_id' => $requestId,
            'user_id' => $userId,
        ];

        if ($applyId) {
            $data['apply_id'] = $applyId;
        }

        $response = $this->makeRequest('POST', '/v1/card/apply-result', $data);

        $this->logAction('query_apply_result', $data, $response);

        return CardApplicationResponse::fromArray($response);
    }

    /**
     * Get card information
     * According to MusePay API: POST /v1/card/query
     *
     * @param string $cardId The card ID of the card issued
     * @param string $userId The unique id in musewallet
     * @return CardInfoResponse
     * @throws MuseWalletException
     */
    public function getCard(string $cardId, string $userId): CardInfoResponse
    {
        $data = [
            'card_id' => $cardId,
            'user_id' => $userId
        ];

        $response = $this->makeRequest('POST', '/v1/card/query', $data);

        $this->logAction('get_card', $data, $response);

        return CardInfoResponse::fromArray($response);
    }

    /**
     * Legacy method - Query card application status
     * @deprecated Use queryApplyResult() instead
     *
     * @param string $applicationId
     * @return array
     * @throws MuseWalletException
     */
    public function queryCardApplicationStatus(string $applicationId): array
    {
        // This is a legacy method, kept for backwards compatibility
        // MusePay API uses request_id and user_id, not application_id
        throw new MuseWalletException('This method is deprecated. Use queryApplyResult(requestId, userId, applyId) instead.');
    }

    /**
     * Activate card
     * According to MusePay API: POST /v1/card/activate
     *
     * @param array $data
     * @return CardInfoResponse
     * @throws MuseWalletException
     */
    public function activateCard(array $data): CardInfoResponse
    {
        $response = $this->makeRequest('POST', '/v1/card/activate', $data);

        $this->logAction('activate_card', $data, $response);

        return CardInfoResponse::fromArray($response);
    }

    /**
     * Top up card
     * According to MusePay API: POST /v1/cardaccount/topup
     *
     * @param array $data
     * @return TopUpResponse
     * @throws MuseWalletException
     */
    public function topUpCard(array $data): TopUpResponse
    {
        $response = $this->makeRequest('POST', '/v1/cardaccount/topup', $data);

        $this->logAction('topup_card', $data, $response);

        return TopUpResponse::fromArray($response);
    }

    /**
     * Upload KYC documents
     * According to MusePay API: POST /v1/carduser/upload-kyc
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-user
     *
     * @param array $kycData
     * @return CardHolderResponse
     * @throws MuseWalletException
     */
    public function uploadKyc(array $kycData): CardHolderResponse
    {
        $response = $this->makeRequest('POST', '/v1/carduser/upload-kyc', $kycData);

        $this->logAction('upload_kyc', $kycData, $response);

        return CardHolderResponse::fromArray($response);
    }

    /**
     * Generate KYC link
     * According to MusePay API: POST /v1/carduser/kyc-link
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-user
     *
     * @param string $userXid
     * @return KycLinkResponse
     * @throws MuseWalletException
     */
    public function generateKycLink(string $userXid): KycLinkResponse
    {
        $response = $this->makeRequest('POST', '/v1/carduser/kyc-link', [
            'user_xid' => $userXid
        ]);

        $this->logAction('generate_kyc_link', ['user_xid' => $userXid], $response);

        return KycLinkResponse::fromArray($response);
    }

    /**
     * Process webhook notification from MusePay
     *
     * @param array $payload
     * @param string $signature
     * @return array
     * @throws MuseWalletException
     */
    public function processWebhook(array $payload, string $signature): array
    {
        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            throw new MuseWalletException('Invalid webhook signature');
        }

        $eventType = $payload['type'] ?? $payload['event_type'] ?? 'unknown';

        Log::info('MuseWallet webhook processed', [
            'event_type' => $eventType,
            'payload' => $payload
        ]);

        // Dispatch event based on type
        if ($this->eventsEnabled && config('musewallet.events.dispatch_on_webhook', true)) {
            $this->dispatchWebhookEvent($payload, $signature);
        }

        return [
            'status' => 'processed',
            'event_type' => $eventType
        ];
    }

    /**
     * Dispatch appropriate event based on webhook type
     *
     * @param array $payload
     * @param string $signature
     * @return void
     */
    protected function dispatchWebhookEvent(array $payload, string $signature): void
    {
        $eventType = $payload['type'] ?? $payload['event_type'] ?? '';

        // MusePay API webhook types from documentation
        $eventMap = [
            // APPLY_AUDIT - Card Apply Message (approval/rejection)
            'APPLY_AUDIT' => ApplicationApprovedEvent::class, // Will check status inside

            // CARD_TOP_UP - Card Top-Up Order Message
            'CARD_TOP_UP' => TopUpCompletedEvent::class,

            // CARD_BILL_TRANSACTION - Card Bill Transaction Message
            'CARD_BILL_TRANSACTION' => TransactionCompletedEvent::class,

            // Legacy/custom event types (for backward compatibility)
            'card.created' => CardCreatedEvent::class,
            'card.activated' => CardActivatedEvent::class,
            'card.blocked' => CardBlockedEvent::class,
            'transaction.completed' => TransactionCompletedEvent::class,
            'transaction.failed' => TransactionFailedEvent::class,
            'topup.completed' => TopUpCompletedEvent::class,
            'kyc.approved' => KycApprovedEvent::class,
            'kyc.rejected' => KycRejectedEvent::class,
            'application.approved' => ApplicationApprovedEvent::class,
            'application.rejected' => ApplicationRejectedEvent::class,
        ];

        if (isset($eventMap[$eventType])) {
            $eventClass = $eventMap[$eventType];
            Event::dispatch(new $eventClass($payload, $signature));

            Log::info('MuseWallet event dispatched', [
                'event_type' => $eventType,
                'event_class' => $eventClass
            ]);
        }
    }

    /**
     * Make HTTP request to MusePay API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws MuseWalletException
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        // Generate signature with ALL data (as per Java MapUtils.convert example)
        $preparedData = $this->generateSignatureRequestData($data, $endpoint);

        // Merge signature data (partner_id, timestamp, nonce, sign, sign_type) with original data
        $data = array_merge($data, $preparedData);


        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->retryAttempts) {
            try {
                $response = Http::asJson()
                    ->acceptJson()
                    ->timeout($this->timeout)
                    ->{strtolower($method)}($url, $data);

                Log::info('MuseWallet API response', [
                    'response' => $response->json(),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                $this->handleErrorResponse($response);

            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt < $this->retryAttempts) {
                    sleep(pow(2, $attempt)); // Exponential backoff
                }
            }
        }

        throw new MuseWalletException(
            'API request failed after ' . $this->retryAttempts . ' attempts: ' .
            ($lastException ? $lastException->getMessage() : 'Unknown error')
        );
    }

    /**
     * Generate RSA SHA1 signature for MusePay API
     * Based on Java example: MapUtils.convert(request) - includes ALL fields except 'sign'
     *
     * @param array $data Original request data
     * @param string $endpoint API endpoint for logging
     * @return array Returns signature parameters to merge
     * @throws MuseWalletException
     */
    protected function generateSignatureRequestData(array $data, string $endpoint): array
    {
        // Add common parameters
        $data['partner_id'] = $this->partnerId;
        $data['sign_type'] = 'RSA';
        $data['timestamp'] = (string)time();
        $data['nonce'] = $data['timestamp'];

        // Extract only the fields that should be included in signature for this endpoint
        $signatureParams = SignatureParameters::extract($endpoint, $data);

        // Build query string WITHOUT URL encoding (as per Java: MapUtils.joinMap)
        $parts = [];
        foreach ($signatureParams as $key => $value) {
            $parts[] = $key . '=' . $value;
        }
        $message = implode('&', $parts);

        Log::info('MuseWallet signature', [
            'endpoint' => $endpoint,
            'message' => $message, // Full message for debugging
            'fields' => array_keys($signatureParams),
            'message_length' => strlen($message)
        ]);

        // Sign with RSA SHA1 using private key
        $privateKey = openssl_pkey_get_private($this->privateKey);
        if (!$privateKey) {
            throw new MuseWalletException('Invalid private key provided');
        }

        openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        $signBase64 = base64_encode($signature);

        Log::info('MuseWallet signature generated', [
            'endpoint' => $endpoint,
            'sign' => substr($signBase64, 0, 50) . '...'
        ]);

        // Return only auth params to merge with original data
        return [
            'partner_id' => $this->partnerId,
            'sign_type' => 'RSA',
            'timestamp' => $data['timestamp'],
            'nonce' => $data['nonce'],
            'sign' => $signBase64
        ];
    }

    /**
     * Verify webhook signature
     *
     * @param array $payload
     * @param string $signature
     * @return bool
     */
    protected function verifyWebhookSignature(array $payload, string $signature): bool
    {
        if ($this->testingMode) {
            return true; // Skip verification in testing mode
        }

        $webhookSecret = config('musewallet.webhooks.secret');
        if (empty($webhookSecret)) {
            return true; // No secret configured
        }

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle API error response
     *
     * @param \Illuminate\Http\Client\Response $response
     * @throws MuseWalletException
     */
    protected function handleErrorResponse($response): void
    {
        $statusCode = $response->status();
        $body = $response->json();

        $errorMessage = $body['message'] ?? $body['error'] ?? 'Unknown error';

        Log::error('MuseWallet API error', [
            'status_code' => $statusCode,
            'error' => $errorMessage,
            'response' => $body
        ]);

        throw new MuseWalletException(
            "API request failed with status {$statusCode}: {$errorMessage}",
            $statusCode
        );
    }

    /**
     * Log action for debugging and monitoring
     *
     * @param string $action
     * @param array $input
     * @param array $output
     * @return void
     */
    protected function logAction(string $action, array $input, array $output): void
    {
        if (!config('musewallet.logging.enabled')) {
            return;
        }

        Log::channel(config('musewallet.logging.channel'))
            ->info("MuseWallet action: {$action}", [
                'action' => $action,
                'input' => $input,
                'output' => $output,
            ]);
    }
}

