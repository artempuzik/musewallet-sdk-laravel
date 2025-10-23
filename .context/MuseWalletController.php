<?php

namespace App\Http\Controllers\Api\V1;

use MuseWallet\SDK\Http\Controllers\MuseWalletController as BaseMuseWalletController;

/**
 *
 * Extends the base SDK controller. You can override any method to customize behavior.
 * By default, all methods from the SDK are available.
 *
 * @example Override a method:
 * public function webhook(Request $request): JsonResponse
 * {
 *     // Custom logic before
 *     $result = parent::webhook($request);
 *     // Custom logic after
 *     return $result;
 * }
 *
 * @example Use service directly:
 * public function customMethod()
 * {
 *     $balance = $this->museWalletService->getPartnerBalance('USDT_TRC20');
 *     // Your custom logic
 * }
 */
class MuseWalletController extends BaseMuseWalletController
{
    /**
     * All methods from MuseWallet\SDK\Http\Controllers\MuseWalletController
     * are available by default:
     * - webhook()
     * - getPartnerBalance()
     * - getCardProducts()
     * - createCardHolder()
     * - applyCard()
     * - queryApplyResult()
     * - getCardInfo()
     * - activateCard()
     * - topUpCard()
     * - uploadKyc()
     * - generateKycLink()
     */

    // Example: Override a method to add custom logic before/after
    /*
    public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
    {
        // Custom pre-processing
        Log::info('Creating card holder in application', $request->validated());

        // Call parent method (SDK's logic with validation)
        $result = parent::createCardHolder($request);

        // Custom post-processing
        // e.g., save user_id to local database
        // User::where('id', auth()->id())->update([
        //     'musewallet_user_id' => $result->getData()->data->user_id
        // ]);

        return $result;
    }
    */

    // Example: Add completely custom method using service
    /*
    public function getMyCards(Request $request): JsonResponse
    {
        $userId = auth()->user()->musewallet_user_id;

        // Use service directly
        $cards = $this->museWalletService->getCard($userId);

        // Your custom logic
        return response()->json([
            'success' => true,
            'cards' => $cards,
            'user' => auth()->user()
        ]);
    }
    */
}
