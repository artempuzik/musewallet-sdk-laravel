<?php

namespace MuseWallet\SDK\Enums;

/**
 * Document Type Enums for MuseWallet API
 *
 * Represents the type of identification document provided for KYC verification.
 */
class DocumentType
{
    /**
     * Passport
     */
    public const PASSPORT = '1';

    /**
     * ID Card / Driver's License
     */
    public const ID_CARD = '2';

    /**
     * Get all document types
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::PASSPORT => 'Passport',
            self::ID_CARD => 'ID Card / Driver\'s License',
        ];
    }

    /**
     * Get document type label
     *
     * @param string $type
     * @return string
     */
    public static function label(string $type): string
    {
        return self::all()[$type] ?? 'Unknown';
    }

    /**
     * Validate document type
     *
     * @param string $type
     * @return bool
     */
    public static function isValid(string $type): bool
    {
        return isset(self::all()[$type]);
    }
}

