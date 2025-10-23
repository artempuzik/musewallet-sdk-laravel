<?php

namespace MuseWallet\SDK\Enums;

/**
 * Supported Currencies for MuseWallet API
 *
 * Based on MusePay documentation for supported assets.
 */
class Currency
{
    /**
     * USDT
     */
    public const USDT = 'USDT';
    /**
     * USDT on Tron network (TRC20)
     */
    public const USDT_TRC20 = 'USDT_TRC20';

    /**
     * Tron native token
     */
    public const TRX = 'TRX';

    /**
     * USDT on Ethereum network (ERC20)
     */
    public const USDT_ERC20 = 'USDT_ERC20';

    /**
     * Ethereum native token
     */
    public const ETH = 'Ethereum';

    /**
     * USDC on Ethereum network (ERC20)
     */
    public const USDC_ERC20 = 'USDC_ERC20';

    /**
     * USDT on BNB Smart Chain
     */
    public const USDT_BSC = 'USDT_BSC';

    /**
     * USDT on Arbitrum
     */
    public const USDT_ARB = 'USDT_ARB';

    /**
     * USDC on Arbitrum
     */
    public const USDC_ARB = 'USDC_ARB';

    /**
     * Get all supported currencies with network info
     *
     * @return array<string, array{name: string, network: string, decimals: int}>
     */
    public static function all(): array
    {
        return [
            self::USDT => [
                'name' => 'USDT',
                'network' => 'USDT',
                'decimals' => 6,
            ],
            self::USDT_TRC20 => [
                'name' => 'USDT (TRC20)',
                'network' => 'Tron',
                'decimals' => 6,
            ],
            self::TRX => [
                'name' => 'TRX',
                'network' => 'Tron',
                'decimals' => 6,
            ],
            self::USDT_ERC20 => [
                'name' => 'USDT (ERC20)',
                'network' => 'Ethereum',
                'decimals' => 6,
            ],
            self::ETH => [
                'name' => 'Ethereum',
                'network' => 'Ethereum',
                'decimals' => 18,
            ],
            self::USDC_ERC20 => [
                'name' => 'USDC (ERC20)',
                'network' => 'Ethereum',
                'decimals' => 6,
            ],
            self::USDT_BSC => [
                'name' => 'USDT (BEP20)',
                'network' => 'BNB Smart Chain',
                'decimals' => 18,
            ],
            self::USDT_ARB => [
                'name' => 'USDT (Arbitrum)',
                'network' => 'Arbitrum',
                'decimals' => 6,
            ],
            self::USDC_ARB => [
                'name' => 'USDC (Arbitrum)',
                'network' => 'Arbitrum',
                'decimals' => 6,
            ],
        ];
    }

    /**
     * Get currency label
     *
     * @param string $currency
     * @return string
     */
    public static function label(string $currency): string
    {
        return self::all()[$currency]['name'] ?? 'Unknown';
    }

    /**
     * Check if currency is supported
     *
     * @param string $currency
     * @return bool
     */
    public static function isSupported(string $currency): bool
    {
        return isset(self::all()[$currency]);
    }

    /**
     * Get currency network
     *
     * @param string $currency
     * @return string
     */
    public static function network(string $currency): string
    {
        return self::all()[$currency]['network'] ?? 'Unknown';
    }
}

