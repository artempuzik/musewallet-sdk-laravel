<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Base MuseWallet API Response
 *
 * All API responses follow this structure
 * Based on MusePay Card API v1
 */
class MuseWalletResponse
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly mixed $data = null
    ) {}

    /**
     * Create from API response array
     *
     * @param array $response
     * @return static
     */
    public static function fromArray(array $response): static
    {
        return new static(
            code: $response['code'] ?? '500',
            message: $response['message'] ?? 'Unknown error',
            data: $response['data'] ?? null
        );
    }

    /**
     * Check if response is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->code === '200';
    }

    /**
     * Get data as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}

