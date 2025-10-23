<?php

namespace MuseWallet\SDK\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Base webhook event for MuseWallet
 *
 * This event is dispatched when a webhook is received from MuseWallet/MusePay
 */
abstract class MuseWalletWebhookEvent
{
    use Dispatchable, SerializesModels;

    public array $payload;
    public string $eventType;
    public string $signature;
    public \DateTimeInterface $receivedAt;

    /**
     * Create a new event instance
     *
     * @param array $payload
     * @param string $signature
     */
    public function __construct(array $payload, string $signature)
    {
        $this->payload = $payload;
        $this->signature = $signature;
        $this->eventType = $payload['type'] ?? $payload['event_type'] ?? 'unknown';
        $this->receivedAt = new \DateTime();
    }

    /**
     * Get the event type
     *
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * Get the webhook payload
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Get the webhook signature
     *
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * Get the data from payload
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->payload['data'] ?? [];
    }
}

