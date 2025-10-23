<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a card application is rejected
 */
class ApplicationRejectedEvent extends MuseWalletWebhookEvent
{
    /**
     * Get application ID
     *
     * @return string|null
     */
    public function getApplicationId(): ?string
    {
        return $this->payload['data']['apply_id'] ?? $this->payload['data']['application_id'] ?? null;
    }

    /**
     * Get request ID
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->payload['data']['request_id'] ?? null;
    }

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

