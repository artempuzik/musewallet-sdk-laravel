<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Partner Balance Response DTO
 *
 * Response structure for /v1/balance/partner endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/partner
 */
class PartnerBalanceResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $currency = null,
        public readonly ?string $balance = null,
        public readonly ?string $availableBalance = null,
        public readonly ?string $freezeBalance = null,
        public readonly ?string $pendingSettleBalance = null,
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
            currency: $data['currency'] ?? null,
            balance: $data['balance'] ?? null,
            availableBalance: $data['availableBalance'] ?? null,
            freezeBalance: $data['freezeBalance'] ?? null,
            pendingSettleBalance: $data['pendingSettleBalance'] ?? null,
            rawData: $data
        );
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
     * Get total balance as float
     *
     * @return float|null
     */
    public function getBalanceFloat(): ?float
    {
        return $this->balance ? (float) $this->balance : null;
    }

    /**
     * Get freeze balance as float
     *
     * @return float|null
     */
    public function getFreezeBalanceFloat(): ?float
    {
        return $this->freezeBalance ? (float) $this->freezeBalance : null;
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
            'currency' => $this->currency,
            'balance' => $this->balance,
            'availableBalance' => $this->availableBalance,
            'freezeBalance' => $this->freezeBalance,
            'pendingSettleBalance' => $this->pendingSettleBalance,
        ];
    }
}

