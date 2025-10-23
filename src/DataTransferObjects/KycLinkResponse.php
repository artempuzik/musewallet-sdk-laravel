<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * KYC Link Response DTO
 *
 * Response structure for /v1/carduser/kyc-link endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/card-user
 */
class KycLinkResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $userXid = null,
        public readonly ?string $link = null,
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
            userXid: $data['user_xid'] ?? null,
            link: $data['link'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get KYC link URL
     *
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Check if link is generated
     *
     * @return bool
     */
    public function hasLink(): bool
    {
        return !empty($this->link);
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
            'user_xid' => $this->userXid,
            'link' => $this->link,
        ];
    }
}

