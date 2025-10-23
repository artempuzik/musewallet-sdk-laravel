<?php

namespace MuseWallet\SDK\Events;

/**
 * Event fired when a transaction fails
 */
class TransactionFailedEvent extends MuseWalletWebhookEvent
{
    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->payload['data']['transaction_id'] ?? null;
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
     * Get transaction amount
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        $amount = $this->payload['data']['amount'] ?? null;
        return $amount !== null ? (float) $amount : null;
    }

    /**
     * Get failure reason
     *
     * @return string|null
     */
    public function getFailureReason(): ?string
    {
        return $this->payload['data']['reason'] ?? $this->payload['data']['failure_reason'] ?? null;
    }
}

