<?php

namespace MuseWallet\SDK\Enums;

/**
 * KYC Status Enums for MuseWallet API
 *
 * Represents the verification status of a card holder's KYC information.
 */
class KycStatus
{
    /**
     * KYC not set / not submitted
     */
    public const NOT_SET = '0';

    /**
     * KYC submitted, waiting for audit
     */
    public const WAIT_AUDIT = '1';

    /**
     * KYC currently being audited
     */
    public const IN_AUDIT = '2';

    /**
     * KYC approved / passed verification
     */
    public const APPROVED = '3';

    /**
     * KYC refused / rejected
     */
    public const REFUSED = '4';

    /**
     * Get all KYC statuses
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::NOT_SET => 'Not Set',
            self::WAIT_AUDIT => 'Waiting for Audit',
            self::IN_AUDIT => 'In Audit',
            self::APPROVED => 'Approved',
            self::REFUSED => 'Refused',
        ];
    }

    /**
     * Get KYC status label
     *
     * @param string $status
     * @return string
     */
    public static function label(string $status): string
    {
        return self::all()[$status] ?? 'Unknown';
    }

    /**
     * Check if KYC is approved
     *
     * @param string $status
     * @return bool
     */
    public static function isApproved(string $status): bool
    {
        return $status === self::APPROVED;
    }

    /**
     * Check if KYC is pending
     *
     * @param string $status
     * @return bool
     */
    public static function isPending(string $status): bool
    {
        return in_array($status, [self::WAIT_AUDIT, self::IN_AUDIT]);
    }

    /**
     * Check if KYC is rejected
     *
     * @param string $status
     * @return bool
     */
    public static function isRejected(string $status): bool
    {
        return $status === self::REFUSED;
    }
}

