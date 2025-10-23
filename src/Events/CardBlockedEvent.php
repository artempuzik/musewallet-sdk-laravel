<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a card is blocked
 */
class CardBlockedEvent extends MuseWalletWebhookEvent
{
    /**
     * Get card ID
     *
     * @return string|null
     */
    public function getCardId(): ?string
    {
        return $this->payload['data']['card_id'] ?? null;
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
     * Get block reason
     *
     * @return string|null
     */
    public function getBlockReason(): ?string
    {
        return $this->payload['data']['reason'] ?? null;
    }

    /**
     * Get blocked timestamp
     *
     * @return string|null
     */
    public function getBlockedAt(): ?string
    {
        return $this->payload['data']['blocked_at'] ?? null;
    }
}

