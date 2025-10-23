<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a card is created
 */
class CardCreatedEvent extends MuseWalletWebhookEvent
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
     * Get card status
     *
     * @return string|null
     */
    public function getCardStatus(): ?string
    {
        return $this->payload['data']['status'] ?? null;
    }
}

