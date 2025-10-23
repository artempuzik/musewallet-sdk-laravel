<?php

namespace MuseWallet\SDK\Enums;

/**
 * Card Application Status Enums for MuseWallet API
 *
 * Represents the status of a card application.
 */
class ApplyStatus
{
    /**
     * Application has been initiated (initial status from API)
     */
    public const CARD_INIT = 'CARD_INIT';

    /**
     * Application is currently being reviewed
     */
    public const CARD_REVIEWING = 'CARD_REVIEWING';

    /**
     * Application is currently being processed
     */
    public const APPLYING = 'APPLYING';

    /**
     * Application has been approved
     */
    public const APPROVED = 'APPROVED';

    /**
     * Card application has been approved by system
     */
    public const CARD_APPROVED = 'CARD_APPROVED';

    /**
     * Application has been rejected
     */
    public const REJECTED = 'REJECTED';

    /**
     * Card application has been rejected
     */
    public const CARD_REJECT = 'CARD_REJECT';

    /**
     * Card has been issued
     */
    public const ISSUED = 'ISSUED';

    /**
     * Card has been shipped to user
     */
    public const CARD_SHIPPED = 'CARD_SHIPPED';

    /**
     * Get all application statuses
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::CARD_INIT => 'Initiated',
            self::CARD_REVIEWING => 'Reviewing',
            self::APPLYING => 'Applying',
            self::APPROVED => 'Approved',
            self::CARD_APPROVED => 'Card Approved',
            self::REJECTED => 'Rejected',
            self::CARD_REJECT => 'Card Rejected',
            self::ISSUED => 'Issued',
            self::CARD_SHIPPED => 'Shipped',
        ];
    }

    /**
     * Get application status label
     *
     * @param string $status
     * @return string
     */
    public static function label(string $status): string
    {
        return self::all()[$status] ?? 'Unknown';
    }

    /**
     * Check if application is successful
     *
     * @param string $status
     * @return bool
     */
    public static function isSuccessful(string $status): bool
    {
        return in_array($status, [self::APPROVED, self::CARD_APPROVED, self::ISSUED, self::CARD_SHIPPED]);
    }

    /**
     * Check if application is pending
     *
     * @param string $status
     * @return bool
     */
    public static function isPending(string $status): bool
    {
        return in_array($status, [self::CARD_INIT, self::CARD_REVIEWING, self::APPLYING]);
    }

    /**
     * Check if application is rejected
     *
     * @param string $status
     * @return bool
     */
    public static function isRejected(string $status): bool
    {
        return in_array($status, [self::REJECTED, self::CARD_REJECT]);
    }
}

