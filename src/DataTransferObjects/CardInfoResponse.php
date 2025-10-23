<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Card Information Response DTO
 *
 * Response structure for /v1/card/query endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/card
 */
class CardInfoResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $cardId = null,
        public readonly ?string $userId = null,
        public readonly ?string $cardNumber = null,
        public readonly ?string $status = null,
        public readonly ?string $cardLevel = null,
        public readonly ?string $cardType = null,
        public readonly ?string $currency = null,
        public readonly ?string $balance = null,
        public readonly ?string $availableBalance = null,
        public readonly ?string $embossedName = null,
        public readonly ?string $expiryMonth = null,
        public readonly ?string $expiryYear = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $phoneAreaCode = null,
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
            cardId: $data['card_id'] ?? null,
            userId: $data['user_id'] ?? null,
            cardNumber: $data['card_number'] ?? null,
            status: $data['status'] ?? null,
            cardLevel: $data['card_level'] ?? null,
            cardType: $data['card_type'] ?? null,
            currency: $data['currency'] ?? null,
            balance: $data['balance'] ?? null,
            availableBalance: $data['available_balance'] ?? null,
            embossedName: $data['embossed_name'] ?? null,
            expiryMonth: $data['expiry_month'] ?? null,
            expiryYear: $data['expiry_year'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            phoneAreaCode: $data['phone_area_code'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get card balance as float
     *
     * @return float|null
     */
    public function getBalanceFloat(): ?float
    {
        return $this->balance ? (float) $this->balance : null;
    }

    /**
     * Get available balance as float
     *
     * @return float|null
     */
    public function getAvailableBalanceFloat(): ?float
    {
        return $this->availableBalance ? (float) $this->availableBalance : null;
    }

    /**
     * Get masked card number (last 4 digits)
     *
     * @return string|null
     */
    public function getMaskedCardNumber(): ?string
    {
        if (!$this->cardNumber || strlen($this->cardNumber) < 4) {
            return null;
        }

        return '****' . substr($this->cardNumber, -4);
    }

    /**
     * Check if card is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
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
            'card_id' => $this->cardId,
            'user_id' => $this->userId,
            'card_number' => $this->cardNumber,
            'status' => $this->status,
            'card_level' => $this->cardLevel,
            'card_type' => $this->cardType,
            'currency' => $this->currency,
            'balance' => $this->balance,
            'available_balance' => $this->availableBalance,
            'embossed_name' => $this->embossedName,
            'expiry_month' => $this->expiryMonth,
            'expiry_year' => $this->expiryYear,
            'phone_number' => $this->phoneNumber,
            'phone_area_code' => $this->phoneAreaCode,
        ];
    }
}

