<?php

namespace MuseWallet\SDK\Services;

/**
 * Signature Parameters Configuration for MuseWallet API
 *
 * Defines which fields should be included in the signature for each endpoint.
 * Based on MusePay API documentation and Java SDK examples.
 */
class SignatureParameters
{
    /**
     * Common parameters that are always included in the signature
     */
    private const COMMON_SIGNATURE_FIELDS = [
        'partner_id',
        'sign_type',
        'timestamp',
        'nonce',
    ];

    /**
     * Endpoint-specific signature fields mapping
     *
     * Key: endpoint path
     * Value: array of business parameter field names to include in signature
     *
     * Note: Only TOP-LEVEL scalar fields should be listed here.
     * Nested objects (individual, document, address) are included automatically.
     */
    private const ENDPOINT_SIGNATURE_FIELDS = [
        // Card User endpoints
        '/v1/carduser/create' => [
            'user_xid',
            'email',
            'individual',
            'document',
            'address',
            'user_name',
        ],

        '/v1/carduser/query' => [
            'user_id',
            'user_xid',
            'email',
            'phone_number',
        ],

        '/v1/carduser/upload-kyc' => [
            'user_xid',
            'individual',
            'document',
            'address',
        ],

        '/v1/carduser/kyc-link' => [
            'user_xid',
        ],

        // Card endpoints
        '/v1/card/apply' => [
            'user_id',
            'request_id',
            'card_product_id',
            'card_level',
            'phone_number',
            'phone_area_code',
            'embossed_name',
        ],

        '/v1/card/apply-result' => [
            'request_id',
            'user_id',
        ],

        '/v1/card/query' => [
            'card_id',
            'user_id',
        ],

        '/v1/card/activate' => [
            'user_id',
            'card_id',
        ],

        // Card Account endpoints
        '/v1/cardaccount/topup' => [
            'request_id',
            'card_id',
            'user_id',
            'amount',
            'currency',
        ],

        // Balance endpoints
        '/v1/balance/partner' => [
            'currency',
        ],

        '/v1/balance/partner-address' => [
            'currency',
            'description',
        ],

        // Card management endpoints
        '/v1/card/replace' => [
            'user_id',
            'original_card_id',
            'replace_reason',
            'request_id',
        ],

        '/v1/card/limitChange' => [
            'user_id',
            'card_id',
            'daily_purchase_limit',
        ],

        '/v1/card/txn-verification-confirm' => [
            'user_id',
            'card_id',
            'token',
            'request_id',
        ],

        '/v1/card/txn-verification-decline' => [
            'user_id',
            'card_id',
            'token',
            'request_id',
        ],
    ];

    /**
     * Get signature fields for specific endpoint
     * Returns array of field names that should be included in signature
     *
     * @param string $endpoint
     * @return array Field names to include in signature
     */
    public static function getFieldsForEndpoint(string $endpoint): array
    {
        // Get business parameter fields for this endpoint
        $businessFields = self::ENDPOINT_SIGNATURE_FIELDS[$endpoint] ?? [];

        // Merge with common fields
        return array_merge(self::COMMON_SIGNATURE_FIELDS, $businessFields);
    }

    /**
     * Check if a field should be included in signature for given endpoint
     *
     * @param string $endpoint
     * @param string $fieldName
     * @return bool
     */
    public static function shouldIncludeField(string $endpoint, string $fieldName): bool
    {
        $fields = self::getFieldsForEndpoint($endpoint);
        return in_array($fieldName, $fields, true);
    }

    /**
     * Extract signature parameters from request data based on endpoint
     *
     * This method extracts ONLY the fields that should be included in the signature:
     * 1. Common parameters (partner_id, sign_type, timestamp, nonce)
     * 2. Business parameters defined for the endpoint (user_id, request_id, etc.)
     * 3. Complex objects (individual, document, address) as JSON strings
     *
     * @param string $endpoint API endpoint path
     * @param array $data Request data
     * @return array Parameters to include in signature
     */
    public static function extract(string $endpoint, array $data): array
    {
        $signatureFields = self::getFieldsForEndpoint($endpoint);

        $signatureData = [];
        foreach ($signatureFields as $key => $value) {
            if (!isset($data[$value])) {
                continue;
            }
            $val = $data[$value];
            // Convert arrays/objects to JSON strings (like Java does)
            if (is_array($val) || is_object($val)) {
                ksort($val);
                $jsonValue = json_encode($val, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                // Skip empty arrays/objects
                if ($jsonValue && $jsonValue !== '[]' && $jsonValue !== '{}' && $jsonValue !== 'null') {
                    $signatureData[$value] = $jsonValue;
                }
            } else {
                // Skip empty scalar values
                if ($val !== null && $val !== '') {
                    $signatureData[$value] = $val;
                }
            }
        }
        // Sort parameters alphabetically by key
        ksort($signatureData);
        return $signatureData;
    }
}

