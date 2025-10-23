<?php

namespace MuseWallet\SDK\Enums;

/**
 * Card Status Enums for MuseWallet API
 *
 * Represents the current status of a card.
 */
class CardStatus
{
    /**
     * Card is inactive (not yet activated)
     */
    public const INACTIVE = 'INACTIVE';

    /**
     * Card is active and can be used
     */
    public const ACTIVE = 'ACTIVE';

    /**
     * Card is temporarily locked by user
     */
    public const LOCKED = 'LOCKED';

    /**
     * Card is permanently closed
     */
    public const CLOSED = 'CLOSED';

    /**
     * Card is suspended by system
     */
    public const SUSPENDED = 'SUSPENDED';

    /**
     * Get all card statuses
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            self::LOCKED => 'Locked',
            self::CLOSED => 'Closed',
            self::SUSPENDED => 'Suspended',
        ];
    }

    /**
     * Get card status label
     *
     * @param string $status
     * @return string
     */
    public static function label(string $status): string
    {
        return self::all()[$status] ?? 'Unknown';
    }

    /**
     * Check if card is usable
     *
     * @param string $status
     * @return bool
     */
    public static function isUsable(string $status): bool
    {
        return $status === self::ACTIVE;
    }

    /**
     * Check if card can be activated
     *
     * @param string $status
     * @return bool
     */
    public static function canBeActivated(string $status): bool
    {
        return $status === self::INACTIVE;
    }
}

