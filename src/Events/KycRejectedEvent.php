<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when KYC verification is rejected
 */
class KycRejectedEvent extends MuseWalletWebhookEvent
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
     * Get rejection reason
     *
     * @return string|null
     */
    public function getRejectionReason(): ?string
    {
        return $this->payload['data']['reason'] ?? $this->payload['data']['rejection_reason'] ?? null;
    }

    /**
     * Get rejected timestamp
     *
     * @return string|null
     */
    public function getRejectedAt(): ?string
    {
        return $this->payload['data']['rejected_at'] ?? null;
    }
}

