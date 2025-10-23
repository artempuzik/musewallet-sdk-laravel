<?php

namespace MuseWallet\SDK\Enums;

/**
 * Card Level Enums for MuseWallet API
 *
 * Represents the tier/level of the card being issued.
 */
class CardLevel
{
    /**
     * Card Level 1
     */
    public const LEVEL_1 = '1';

    /**
     * Card Level 2
     */
    public const LEVEL_2 = '2';

    /**
     * Card Level 3
     */
    public const LEVEL_3 = '3';

    /**
     * Card Level 4
     */
    public const LEVEL_4 = '4';

    /**
     * Card Level 5
     */
    public const LEVEL_5 = '5';

    /**
     * Get all card levels
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::LEVEL_1 => 'Level 1',
            self::LEVEL_2 => 'Level 2',
            self::LEVEL_3 => 'Level 3',
            self::LEVEL_4 => 'Level 4',
            self::LEVEL_5 => 'Level 5',
        ];
    }

    /**
     * Get card level label
     *
     * @param string $level
     * @return string
     */
    public static function label(string $level): string
    {
        return self::all()[$level] ?? 'Unknown';
    }

    /**
     * Validate card level
     *
     * @param string $level
     * @return bool
     */
    public static function isValid(string $level): bool
    {
        return isset(self::all()[$level]);
    }
}

