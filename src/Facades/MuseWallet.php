<?php

namespace MuseWallet\SDK\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * MuseWallet Facade
 *
 * Provides static access to MuseWallet service methods
 * Based on MusePay Card API v1
 *
 * @method static \MuseWallet\SDK\DataTransferObjects\PartnerBalanceResponse getPartnerBalance(string $currency = 'USDT')
 * @method static array getCardProducts()
 * @method static \MuseWallet\SDK\DataTransferObjects\CardHolderResponse createCardHolder(array $holderData)
 * @method static \MuseWallet\SDK\DataTransferObjects\CardApplicationResponse applyCard(array $applicationData)
 * @method static \MuseWallet\SDK\DataTransferObjects\CardApplicationResponse queryApplyResult(string $requestId, string $userId, ?string $applyId = null)
 * @method static \MuseWallet\SDK\DataTransferObjects\CardInfoResponse getCard(string $cardId, string $userId)
 * @method static \MuseWallet\SDK\DataTransferObjects\CardInfoResponse activateCard(array $data)
 * @method static \MuseWallet\SDK\DataTransferObjects\TopUpResponse topUpCard(array $data)
 * @method static \MuseWallet\SDK\DataTransferObjects\CardHolderResponse uploadKyc(array $kycData)
 * @method static \MuseWallet\SDK\DataTransferObjects\KycLinkResponse generateKycLink(string $userXid)
 * @method static array processWebhook(array $payload, string $signature)
 *
 * @see \MuseWallet\SDK\Services\MuseWalletService
 */
class MuseWallet extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'musewallet.api';
    }
}

