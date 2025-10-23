<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Card Application Response DTO
 *
 * Response structure for /v1/card/apply endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/card
 */
class CardApplicationResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $applyId = null,
        public readonly ?string $requestId = null,
        public readonly ?string $userId = null,
        public readonly ?string $status = null,
        public readonly ?string $cardId = null,
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
            applyId: $data['apply_id'] ?? null,
            requestId: $data['request_id'] ?? null,
            userId: $data['user_id'] ?? null,
            status: $data['status'] ?? null,
            cardId: $data['card_id'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get application ID
     *
     * @return string|null
     */
    public function getApplyId(): ?string
    {
        return $this->applyId;
    }

    /**
     * Get request ID
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * Get application status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get card ID (if issued)
     *
     * @return string|null
     */
    public function getCardId(): ?string
    {
        return $this->cardId;
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
            'apply_id' => $this->applyId,
            'request_id' => $this->requestId,
            'user_id' => $this->userId,
            'status' => $this->status,
            'card_id' => $this->cardId,
        ];
    }
}

