<?php

namespace MuseWallet\SDK\Http\Controllers;

use MuseWallet\SDK\Services\MuseWalletService;
use MuseWallet\SDK\Exceptions\MuseWalletException;
use MuseWallet\SDK\Http\Requests\{
    CreateCardHolderRequest,
    ApplyCardRequest,
    QueryApplyResultRequest,
    GetCardInfoRequest,
    ActivateCardRequest,
    TopUpCardRequest,
    UploadKycRequest,
    GenerateKycLinkRequest
};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

/**
 * Base MuseWallet API Controller
 *
 * This controller provides ready-to-use endpoints for MuseWallet integration.
 * You can use it directly in your routes or extend it in your application
 * to customize the behavior.
 *
 * @example Direct usage in routes:
 * Route::post('/musewallet/webhook', [\MuseWallet\SDK\Http\Controllers\MuseWalletController::class, 'webhook']);
 *
 * @example Extending in your app:
 * class MyMuseWalletController extends \MuseWallet\SDK\Http\Controllers\MuseWalletController
 * {
 *     public function webhook(Request $request): JsonResponse
 *     {
 *         // Custom pre-processing
 *         $result = parent::webhook($request);
 *         // Custom post-processing
 *         return $result;
 *     }
 * }
 */
class MuseWalletController extends Controller
{
    protected MuseWalletService $museWalletService;

    public function __construct(MuseWalletService $museWalletService)
    {
        $this->museWalletService = $museWalletService;
    }

    /**
     * Handle webhook notifications from MuseWallet
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $signature = $request->header('X-Webhook-Signature', '');

            Log::info('MuseWallet webhook received', [
                'payload' => $payload,
                'signature' => $signature,
                'headers' => $request->headers->all()
            ]);

            $result = $this->museWalletService->processWebhook($payload, $signature);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'result' => $result
            ]);

        } catch (MuseWalletException $e) {
            Log::error('MuseWallet webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Unexpected error processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get partner balance
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPartnerBalance(Request $request): JsonResponse
    {
        try {
            $currency = $request->input('currency', 'USDT');
            $balance = $this->museWalletService->getPartnerBalance($currency);

            return response()->json([
                'success' => true,
                'data' => $balance->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get card products
     *
     * @return JsonResponse
     */
    public function getCardProducts(): JsonResponse
    {
        try {
            $products = $this->museWalletService->getCardProducts();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create card holder
     * According to MusePay API: POST /v1/carduser/create
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-user
     *
     * @param CreateCardHolderRequest $request
     * @return JsonResponse
     */
    public function createCardHolder(CreateCardHolderRequest $request): JsonResponse
    {
        try {
            $result = $this->museWalletService->createCardHolder($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply for a card
     * According to MusePay API: POST /v1/card/apply
     * Docs: https://docs-card.musepay.io/reference/api-reference/card#apply-card
     *
     * @param ApplyCardRequest $request
     * @return JsonResponse
     */
    public function applyCard(ApplyCardRequest $request): JsonResponse
    {
        try {
            $result = $this->museWalletService->applyCard($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Query card application result
     * According to MusePay API: POST /v1/card/apply-result
     * Docs: https://docs-card.musepay.io/reference/api-reference/card#query-apply-result
     *
     * @param QueryApplyResultRequest $request
     * @return JsonResponse
     */
    public function queryApplyResult(QueryApplyResultRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->queryApplyResult(
                $data['request_id'],
                $data['user_id'],
                $data['apply_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get card information
     * According to MusePay API: POST /v1/card/query
     * Docs: https://docs-card.musepay.io/reference/api-reference/card#get-card
     *
     * @param GetCardInfoRequest $request
     * @return JsonResponse
     */
    public function getCardInfo(GetCardInfoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->getCard($data['card_id'], $data['user_id']);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Activate card
     * According to MusePay API: POST /v1/card/activate
     * Docs: https://docs-card.musepay.io/reference/api-reference/card#activate-card
     *
     * @param ActivateCardRequest $request
     * @return JsonResponse
     */
    public function activateCard(ActivateCardRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->activateCard($data);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Top up card
     * According to MusePay API: POST /v1/cardaccount/topup
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-account
     *
     * @param TopUpCardRequest $request
     * @return JsonResponse
     */
    public function topUpCard(TopUpCardRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->topUpCard($data);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Upload KYC documents
     * According to MusePay API: POST /v1/carduser/upload-kyc
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-user
     *
     * @param UploadKycRequest $request
     * @return JsonResponse
     */
    public function uploadKyc(UploadKycRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->uploadKyc($data);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate KYC link
     * According to MusePay API: POST /v1/carduser/kyc-link
     * Docs: https://docs-card.musepay.io/reference/api-reference/card-user
     *
     * @param GenerateKycLinkRequest $request
     * @return JsonResponse
     */
    public function generateKycLink(GenerateKycLinkRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->museWalletService->generateKycLink($data['user_xid']);

            return response()->json([
                'success' => true,
                'data' => $result->toArray()
            ]);

        } catch (MuseWalletException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}

