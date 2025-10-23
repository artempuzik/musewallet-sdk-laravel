<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when KYC verification is approved
 */
class KycApprovedEvent extends MuseWalletWebhookEvent
{
    /**
     * Get user ID
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->payload['data']['user_id'] ?? null;
    }

    /**
     * Get user XID
     *
     * @return string|null
     */
    public function getUserXid(): ?string
    {
        return $this->payload['data']['user_xid'] ?? null;
    }

    /**
     * Get KYC level
     *
     * @return string|null
     */
    public function getKycLevel(): ?string
    {
        return $this->payload['data']['kyc_level'] ?? null;
    }

    /**
     * Get approval timestamp
     *
     * @return string|null
     */
    public function getApprovedAt(): ?string
    {
        return $this->payload['data']['approved_at'] ?? null;
    }
}

