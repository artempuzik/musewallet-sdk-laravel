<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a card is activated
 */
class CardActivatedEvent extends MuseWalletWebhookEvent
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
     * Get activation timestamp
     *
     * @return string|null
     */
    public function getActivatedAt(): ?string
    {
        return $this->payload['data']['activated_at'] ?? null;
    }
}

