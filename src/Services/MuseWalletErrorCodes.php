<?php

namespace MuseWallet\SDK\Services;

/**
 * MuseWallet API Error Codes
 *
 * Centralized error code definitions and human-readable messages
 * for MuseWallet/MusePay API responses.
 */
class MuseWalletErrorCodes
{
    /**
     * Success response
     */
    public const SUCCESS = '200';

    /**
     * Bad Request - Invalid parameters
     */
    public const BAD_REQUEST = '400';

    /**
     * Signature Error - Incorrect signature
     */
    public const SIGN_ERROR = '406';

    /**
     * Wrong timestamp or nonce
     */
    public const WRONG_TIMESTAMP_OR_NONCE = '412';

    /**
     * System Error - Internal server error
     */
    public const SYSTEM_ERROR = '500';

    /**
     * User status invalid
     */
    public const USER_STATUS_INVALID = '5004';

    /**
     * Order not exist
     */
    public const ORDER_NOT_EXIST = '7000';

    /**
     * Currency not supported
     */
    public const CURRENCY_NOT_SUPPORT = '7002';

    /**
     * Insufficient balance
     */
    public const INSUFFICIENT_BALANCE = '202203';

    /**
     * Quota minimum check failed
     */
    public const QUOTA_MIN_CHECK_FAIL = '202206';

    /**
     * Unsupported currency
     */
    public const UNSUPPORTED_CURRENCY = '202211';

    /**
     * Order amount must be more than service fee
     */
    public const ORDER_AMOUNT_MUST_MORE_THAN_SERVICE_FEE = '202212';

    /**
     * Double payment detected
     */
    public const DOUBLE_PAYMENT = '202224';

    /**
     * No specified fee rule
     */
    public const NO_SPECIFIED_FEE_RULE = '2204002';

    /**
     * KYC level too low for card application
     */
    public const KYC_LEVEL_LOW = '10211001';

    /**
     * Phone number error during card application
     */
    public const PHONE_NUMBER_ERROR = '10211027';

    /**
     * Card cannot be activated (card already active or wrong status)
     */
    public const CARD_NOT_ALLOW_ACTIVATE = '10213005';

    /**
     * Repeated request (duplicate request_id)
     */
    public const REPEATED_REQUEST = '10240000';

    /**
     * All error code mappings with descriptions
     *
     * @return array<string, array{message: string, description: string, suggestion: string}>
     */
    public static function all(): array
    {
        return [
            self::SUCCESS => [
                'message' => 'Success',
                'description' => 'Request completed successfully',
                'suggestion' => '',
            ],
            self::BAD_REQUEST => [
                'message' => 'Bad Request',
                'description' => 'Invalid parameters provided',
                'suggestion' => 'Check the request parameters and ensure all required fields are present and valid',
            ],
            self::SIGN_ERROR => [
                'message' => 'Signature Error',
                'description' => 'The request signature is incorrect',
                'suggestion' => 'Verify that the private key is correct and the signature generation algorithm matches MusePay requirements',
            ],
            self::WRONG_TIMESTAMP_OR_NONCE => [
                'message' => 'Wrong Timestamp or Nonce',
                'description' => 'The timestamp or nonce value is invalid',
                'suggestion' => 'Ensure timestamp is current Unix timestamp and nonce is unique for each request',
            ],
            self::SYSTEM_ERROR => [
                'message' => 'System Error',
                'description' => 'Internal server error on MusePay side',
                'suggestion' => 'Contact MusePay support if the error persists',
            ],
            self::USER_STATUS_INVALID => [
                'message' => 'User Status Invalid',
                'description' => 'The user account status is not valid for this operation',
                'suggestion' => 'Check the user account status and ensure it is active',
            ],
            self::ORDER_NOT_EXIST => [
                'message' => 'Order Not Exist',
                'description' => 'The specified order does not exist',
                'suggestion' => 'Verify the order ID and ensure it was created successfully',
            ],
            self::CURRENCY_NOT_SUPPORT => [
                'message' => 'Currency Not Supported',
                'description' => 'The specified currency is not supported',
                'suggestion' => 'Use one of the supported currencies: USDT_TRC20, USDT_ERC20, USDC_ERC20, etc.',
            ],
            self::INSUFFICIENT_BALANCE => [
                'message' => 'Insufficient Balance',
                'description' => 'The account does not have sufficient balance for this operation',
                'suggestion' => 'Top up the account balance before proceeding',
            ],
            self::QUOTA_MIN_CHECK_FAIL => [
                'message' => 'Quota Minimum Check Failed',
                'description' => 'The amount does not meet the minimum quota requirement',
                'suggestion' => 'Increase the amount to meet the minimum quota requirement',
            ],
            self::UNSUPPORTED_CURRENCY => [
                'message' => 'Unsupported Currency',
                'description' => 'The currency is not supported for this operation',
                'suggestion' => 'Use a supported currency for this card product',
            ],
            self::ORDER_AMOUNT_MUST_MORE_THAN_SERVICE_FEE => [
                'message' => 'Order Amount Too Small',
                'description' => 'The order amount must be greater than the service fee',
                'suggestion' => 'Increase the order amount to cover the service fee',
            ],
            self::DOUBLE_PAYMENT => [
                'message' => 'Double Payment',
                'description' => 'This payment has already been processed',
                'suggestion' => 'Use a unique request_id for each payment',
            ],
            self::NO_SPECIFIED_FEE_RULE => [
                'message' => 'No Fee Rule Specified',
                'description' => 'No fee rule is configured for this operation',
                'suggestion' => 'Contact MusePay support to configure fee rules',
            ],
            self::KYC_LEVEL_LOW => [
                'message' => 'KYC Level Too Low',
                'description' => 'The user KYC verification level is insufficient for card issuance',
                'suggestion' => 'Complete KYC verification by uploading documents or using the KYC link. KYC status must be "APPROVED" (status 3) before applying for a card.',
            ],
            self::PHONE_NUMBER_ERROR => [
                'message' => 'Phone Number Error',
                'description' => 'The phone number is invalid or not verified',
                'suggestion' => 'Ensure the phone number is pre-verified by the partner and in correct format (area code without + sign, e.g., "1" for US, "86" for China)',
            ],
            self::CARD_NOT_ALLOW_ACTIVATE => [
                'message' => 'Card Cannot Be Activated',
                'description' => 'The card is not in a status that allows activation. Card may already be ACTIVE or in PENDING_ACTIVE status.',
                'suggestion' => 'Check the card status first using getCard(). Virtual cards are often automatically activated after approval.',
            ],
            self::REPEATED_REQUEST => [
                'message' => 'Repeated Request',
                'description' => 'This request has already been processed (duplicate request_id)',
                'suggestion' => 'Use a unique request_id (UUID) for each new request',
            ],
        ];
    }

    /**
     * Get error information by code
     *
     * @param string $code
     * @return array{message: string, description: string, suggestion: string}
     */
    public static function get(string $code): array
    {
        return self::all()[$code] ?? [
            'message' => 'Unknown Error',
            'description' => "Unknown error code: {$code}",
            'suggestion' => 'Check MusePay API documentation or contact support',
        ];
    }

    /**
     * Get human-readable error message
     *
     * @param string $code
     * @return string
     */
    public static function message(string $code): string
    {
        return self::get($code)['message'];
    }

    /**
     * Get detailed error description
     *
     * @param string $code
     * @return string
     */
    public static function description(string $code): string
    {
        return self::get($code)['description'];
    }

    /**
     * Get suggestion for resolving the error
     *
     * @param string $code
     * @return string
     */
    public static function suggestion(string $code): string
    {
        return self::get($code)['suggestion'];
    }

    /**
     * Check if error code indicates success
     *
     * @param string $code
     * @return bool
     */
    public static function isSuccess(string $code): bool
    {
        return $code === self::SUCCESS;
    }

    /**
     * Check if error is retryable
     *
     * @param string $code
     * @return bool
     */
    public static function isRetryable(string $code): bool
    {
        $retryableCodes = [
            self::SYSTEM_ERROR,
            self::WRONG_TIMESTAMP_OR_NONCE,
        ];

        return in_array($code, $retryableCodes);
    }

    /**
     * Check if error requires user action
     *
     * @param string $code
     * @return bool
     */
    public static function requiresUserAction(string $code): bool
    {
        $userActionCodes = [
            self::KYC_LEVEL_LOW,
            self::PHONE_NUMBER_ERROR,
            self::INSUFFICIENT_BALANCE,
        ];

        return in_array($code, $userActionCodes);
    }

    /**
     * Format error response for API
     *
     * @param string $code
     * @param string|null $additionalInfo
     * @return array{code: string, message: string, description: string, suggestion: string, additional_info: string|null}
     */
    public static function formatError(string $code, ?string $additionalInfo = null): array
    {
        $error = self::get($code);

        return [
            'code' => $code,
            'message' => $error['message'],
            'description' => $error['description'],
            'suggestion' => $error['suggestion'],
            'additional_info' => $additionalInfo,
        ];
    }
}

