<?php

namespace MuseWallet\SDK\Services;

use Illuminate\Http\JsonResponse;

/**
 * MuseWallet API Response Formatter
 *
 * Formats API responses with error codes and human-readable messages
 */
class MuseWalletResponseFormatter
{
    /**
     * Format successful response
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => MuseWalletErrorCodes::SUCCESS,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Format error response from MusePay API
     *
     * @param array $apiResponse
     * @return JsonResponse
     */
    public static function fromApiResponse(array $apiResponse): JsonResponse
    {
        $code = $apiResponse['code'] ?? MuseWalletErrorCodes::SYSTEM_ERROR;
        $errorInfo = MuseWalletErrorCodes::get($code);

        $isSuccess = MuseWalletErrorCodes::isSuccess($code);
        $httpCode = $isSuccess ? 200 : 400;

        return response()->json([
            'success' => $isSuccess,
            'code' => $code,
            'message' => $errorInfo['message'],
            'description' => $errorInfo['description'],
            'suggestion' => $errorInfo['suggestion'],
            'data' => $apiResponse['data'] ?? null,
            'original_message' => $apiResponse['message'] ?? null,
        ], $httpCode);
    }

    /**
     * Format error response
     *
     * @param string $code
     * @param string|null $additionalInfo
     * @param int $httpCode
     * @return JsonResponse
     */
    public static function error(
        string $code,
        ?string $additionalInfo = null,
        int $httpCode = 400
    ): JsonResponse {
        $errorInfo = MuseWalletErrorCodes::get($code);

        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $errorInfo['message'],
            'description' => $errorInfo['description'],
            'suggestion' => $errorInfo['suggestion'],
            'additional_info' => $additionalInfo,
        ], $httpCode);
    }

    /**
     * Format validation error response
     *
     * @param array $errors
     * @return JsonResponse
     */
    public static function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => MuseWalletErrorCodes::BAD_REQUEST,
            'message' => 'Validation Failed',
            'description' => 'The provided data failed validation',
            'errors' => $errors,
        ], 422);
    }

    /**
     * Format exception response
     *
     * @param \Exception $exception
     * @param bool $debug
     * @return JsonResponse
     */
    public static function exception(
        \Exception $exception,
        bool $debug = false
    ): JsonResponse {
        $response = [
            'success' => false,
            'code' => MuseWalletErrorCodes::SYSTEM_ERROR,
            'message' => 'An error occurred',
            'description' => $debug ? $exception->getMessage() : 'Internal server error',
        ];

        if ($debug) {
            $response['trace'] = $exception->getTraceAsString();
        }

        return response()->json($response, 500);
    }

    /**
     * Format response with enhanced error information
     *
     * @param array $apiResponse
     * @return array Enhanced response with error details
     */
    public static function enhanceResponse(array $apiResponse): array
    {
        $code = $apiResponse['code'] ?? MuseWalletErrorCodes::SYSTEM_ERROR;
        $errorInfo = MuseWalletErrorCodes::get($code);

        return array_merge($apiResponse, [
            'error_details' => [
                'code' => $code,
                'message' => $errorInfo['message'],
                'description' => $errorInfo['description'],
                'suggestion' => $errorInfo['suggestion'],
                'is_retryable' => MuseWalletErrorCodes::isRetryable($code),
                'requires_user_action' => MuseWalletErrorCodes::requiresUserAction($code),
            ],
        ]);
    }
}

