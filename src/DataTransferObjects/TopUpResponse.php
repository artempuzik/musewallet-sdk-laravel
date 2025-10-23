<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Card Top-Up Response DTO
 *
 * Response structure for /v1/cardaccount/topup endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/card-account
 */
class TopUpResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $requestId = null,
        public readonly ?string $cardId = null,
        public readonly ?string $userId = null,
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $orderNo = null,
        public readonly ?string $status = null,
        mixed $rawData = null
    ) {
        parent::__construct($code, $message, $rawData);
    }

    /**
     * Create from API response array
     *
     * @param array $response
     * @return static
     */
    public static function fromArray(array $response): static
    {
        $data = $response['data'] ?? [];

        return new static(
            code: $response['code'] ?? '500',
            message: $response['message'] ?? 'Unknown error',
            requestId: $data['request_id'] ?? null,
            cardId: $data['card_id'] ?? null,
            userId: $data['user_id'] ?? null,
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            orderNo: $data['order_no'] ?? null,
            status: $data['status'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get amount as float
     *
     * @return float|null
     */
    public function getAmountFloat(): ?float
    {
        return $this->amount ? (float) $this->amount : null;
    }

    /**
     * Check if top-up is successful
     *
     * @return bool
     */
    public function isTopUpSuccessful(): bool
    {
        return $this->isSuccessful() && ($this->status === 'completed' || $this->status === 'success');
    }

    /**
     * Convert to array for JSON response (without raw data duplication)
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'request_id' => $this->requestId,
            'card_id' => $this->cardId,
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'order_no' => $this->orderNo,
            'status' => $this->status,
        ];
    }
}

