<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a card top-up is completed
 */
class TopUpCompletedEvent extends MuseWalletWebhookEvent
{
    /**
     * Get top-up ID
     *
     * @return string|null
     */
    public function getTopUpId(): ?string
    {
        return $this->payload['data']['topup_id'] ?? $this->payload['data']['request_id'] ?? null;
    }

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
     * Get top-up amount
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        $amount = $this->payload['data']['amount'] ?? null;
        return $amount !== null ? (float) $amount : null;
    }

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->payload['data']['currency'] ?? null;
    }
}

