<?php

namespace MuseWallet\SDK\DataTransferObjects;

/**
 * Card Holder Response DTO
 *
 * Response structure for /v1/carduser/create endpoint
 * Based on MusePay Card API v1 documentation
 *
 * @link https://docs-card.musepay.io/reference/api-reference/card-user
 */
class CardHolderResponse extends MuseWalletResponse
{
    public function __construct(
        string $code,
        string $message,
        public readonly ?string $userXid = null,
        public readonly ?string $userId = null,
        public readonly ?string $kycStatus = null,
        public readonly ?string $email = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $documentType = null,
        public readonly ?string $documentNumber = null,
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
            userId: $data['user_id'] ?? null,
            kycStatus: $data['kyc_status'] ?? null,
            email: $data['email'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            documentType: $data['document_type'] ?? null,
            documentNumber: $data['document_number'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get user ID
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Get user external ID
     *
     * @return string|null
     */
    public function getUserXid(): ?string
    {
        return $this->userXid;
    }

    /**
     * Get KYC status
     *
     * @return string|null
     */
    public function getKycStatus(): ?string
    {
        return $this->kycStatus;
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
            'user_id' => $this->userId,
            'kyc_status' => $this->kycStatus,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
        ];
    }
}

