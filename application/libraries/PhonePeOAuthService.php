<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PhonePeOAuthService {
    
    private $clientId;
    private $clientSecret;
    private $clientVersion;
    private $oauthUrl;
    private $baseUrl;
    
    public function __construct() {
        $this->clientId = defined('PHONEPE_CLIENT_ID') ? PHONEPE_CLIENT_ID : 'M1Y5YWMA86HR';
        $this->clientSecret = defined('PHONEPE_CLIENT_SECRET') ? PHONEPE_CLIENT_SECRET : '168028f5-f3cf-40e3-a320-120926e1dcfb';
        $this->clientVersion = defined('PHONEPE_CLIENT_VERSION') ? PHONEPE_CLIENT_VERSION : '1';
        // Production OAuth endpoint for standard checkout
        $this->oauthUrl = 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token';
        // Base URL for other API calls
        $this->baseUrl = defined('PHONEPE_BASE_URL') ? PHONEPE_BASE_URL : 'https://api.phonepe.com/apis/hermes';
    }
    
    /**
     * Get OAuth Bearer Token following PhonePe Standard Checkout Integration
     * Production endpoint: https://api.phonepe.com/apis/identity-manager/v1/oauth/token
     * Request format: application/x-www-form-urlencoded
     * Response: access_token, expires_at, token_type (O-Bearer)
     */
    public function getBearerToken() {
        // Prepare form-urlencoded payload
        $payload = array(
            'client_id' => $this->clientId,
            'client_version' => $this->clientVersion,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        );
        
        // Build URL-encoded query string
        $postData = http_build_query($payload);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->oauthUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'accept: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);
        
        if ($curl_error) {
            return array(
                'status' => false,
                'message' => 'CURL Error: ' . $curl_error,
                'response' => $response
            );
        }
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            
            // Handle both direct response and nested data response
            $accessToken = null;
            $encryptedAccessToken = null;
            $expiresAt = null;
            $issuedAt = null;
            $tokenType = 'O-Bearer';
            $expiresIn = null;
            $sessionExpiresAt = null;
            
            if ($result && isset($result['access_token'])) {
                // Direct response format
                $accessToken = $result['access_token'];
                $encryptedAccessToken = $result['encrypted_access_token'] ?? null;
                $expiresAt = $result['expires_at'] ?? null;
                $issuedAt = $result['issued_at'] ?? null;
                $tokenType = $result['token_type'] ?? 'O-Bearer';
                $expiresIn = $result['expires_in'] ?? null;
                $sessionExpiresAt = $result['session_expires_at'] ?? null;
            } elseif ($result && isset($result['data']['access_token'])) {
                // Nested data format
                $data = $result['data'];
                $accessToken = $data['access_token'];
                $encryptedAccessToken = $data['encrypted_access_token'] ?? null;
                $expiresAt = $data['expires_at'] ?? null;
                $issuedAt = $data['issued_at'] ?? null;
                $tokenType = $data['token_type'] ?? 'O-Bearer';
                $expiresIn = $data['expires_in'] ?? null;
                $sessionExpiresAt = $data['session_expires_at'] ?? null;
            }
            
            if ($accessToken) {
                return array(
                    'status' => true,
                    'access_token' => $accessToken,
                    'accessToken' => $accessToken, // Backward compatibility
                    'encrypted_access_token' => $encryptedAccessToken,
                    'encryptedAccessToken' => $encryptedAccessToken, // Backward compatibility
                    'expires_at' => $expiresAt,
                    'expiresAt' => $expiresAt, // Backward compatibility
                    'issued_at' => $issuedAt,
                    'issuedAt' => $issuedAt, // Backward compatibility
                    'token_type' => $tokenType,
                    'tokenType' => $tokenType, // Backward compatibility
                    'expires_in' => $expiresIn,
                    'expiresIn' => $expiresIn, // Backward compatibility
                    'session_expires_at' => $sessionExpiresAt,
                    'sessionExpiresAt' => $sessionExpiresAt // Backward compatibility
                );
            }
        }
        
        return array(
            'status' => false,
            'message' => 'Failed to get OAuth token. HTTP Code: ' . $http_code,
            'response' => $response,
            'http_code' => $http_code
        );
    }
    
    /**
     * Create SDK Order Token following PhonePe Standard Checkout Integration
     * Production endpoint: https://api.phonepe.com/apis/pg/checkout/v2/sdk/order
     * Request format: application/json
     * Authorization: O-Bearer <access_token>
     * 
     * @param string $merchantOrderId Unique merchant order ID (max 63 chars, alphanumeric with _ and -)
     * @param int $amount Amount in paisa (minimum 100)
     * @param string $accessToken OAuth Bearer token
     * @param int|null $expireAfter Optional: Order expiry time in seconds (300-3600)
     * @param array|null $metaInfo Optional: Additional metadata (udf1-15)
     * @param array|null $paymentModeConfig Optional: Payment mode configuration
     * @return array Response with orderId, token, state, expireAt
     */
    public function createSDKOrder($merchantOrderId, $amount, $accessToken, $expireAfter = null, $metaInfo = null, $paymentModeConfig = null) {
        // Production endpoint for Create Order Token API
        $orderUrl = 'https://api.phonepe.com/apis/pg/checkout/v2/sdk/order';
        
        // Build payload according to PhonePe API specification
        $payload = array(
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'paymentFlow' => array(
                'type' => 'PG_CHECKOUT'
            )
        );
        
        // Add optional expireAfter if provided
        if ($expireAfter !== null && $expireAfter >= 300 && $expireAfter <= 3600) {
            $payload['expireAfter'] = $expireAfter;
        }
        
        // Add optional metaInfo if provided
        if ($metaInfo !== null && is_array($metaInfo)) {
            $payload['metaInfo'] = $metaInfo;
        }
        
        // Add optional paymentModeConfig if provided
        if ($paymentModeConfig !== null && is_array($paymentModeConfig)) {
            $payload['paymentFlow']['paymentModeConfig'] = $paymentModeConfig;
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $orderUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken,
            'accept: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);
        
        if ($curl_error) {
            return array(
                'status' => false,
                'message' => 'CURL Error: ' . $curl_error,
                'response' => $response
            );
        }
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            
            // Handle both direct response and nested data response
            if ($result && isset($result['orderId']) && isset($result['token'])) {
                // Direct response format
                return array(
                    'status' => true,
                    'orderId' => $result['orderId'],
                    'token' => $result['token'],
                    'state' => $result['state'] ?? 'PENDING',
                    'expireAt' => $result['expireAt'] ?? null,
                    'merchantOrderId' => $merchantOrderId
                );
            } elseif ($result && isset($result['data']['orderId']) && isset($result['data']['token'])) {
                // Nested data format
                $data = $result['data'];
                return array(
                    'status' => true,
                    'orderId' => $data['orderId'],
                    'token' => $data['token'],
                    'state' => $data['state'] ?? 'PENDING',
                    'expireAt' => $data['expireAt'] ?? null,
                    'merchantOrderId' => $merchantOrderId
                );
            }
        }
        
        return array(
            'status' => false,
            'message' => 'Failed to create SDK order. HTTP Code: ' . $http_code,
            'response' => $response,
            'http_code' => $http_code
        );
    }
    
    /**
     * Check Order Status following PhonePe Standard Checkout Integration
     * Production endpoint: https://api.phonepe.com/apis/pg/checkout/v2/order/{merchantOrderId}/status
     * HTTP Method: GET
     * Authorization: O-Bearer <access_token>
     * 
     * @param string $merchantOrderId Merchant order ID to check status for
     * @param string $accessToken OAuth Bearer token
     * @param bool $details Optional: true to return all attempt details, false for only latest (default: false)
     * @param bool $errorContext Optional: true to receive errorContext block for FAILED state (default: false)
     * @return array Response with orderId, state, amount, paymentDetails, etc.
     */
    public function verifyOrderStatus($merchantOrderId, $accessToken, $details = false, $errorContext = false) {
        // Production endpoint for Check Order Status API
        $statusUrl = 'https://api.phonepe.com/apis/pg/checkout/v2/order/' . urlencode($merchantOrderId) . '/status';
        
        // Build query parameters
        $queryParams = array();
        if ($details !== null) {
            $queryParams['details'] = $details ? 'true' : 'false';
        }
        if ($errorContext !== null) {
            $queryParams['errorContext'] = $errorContext ? 'true' : 'false';
        }
        
        // Append query parameters to URL if provided
        if (!empty($queryParams)) {
            $statusUrl .= '?' . http_build_query($queryParams);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $statusUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken,
            'accept: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);
        
        if ($curl_error) {
            return array(
                'status' => false,
                'message' => 'CURL Error: ' . $curl_error,
                'response' => $response
            );
        }
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            
            // Check for error response (invalid order ID)
            if (isset($result['success']) && $result['success'] === false) {
                return array(
                    'status' => false,
                    'success' => false,
                    'code' => $result['code'] ?? 'UNKNOWN_ERROR',
                    'message' => $result['message'] ?? 'Order status check failed',
                    'data' => $result['data'] ?? array(),
                    'http_code' => $http_code
                );
            }
            
            // Handle successful response with order details
            if ($result && isset($result['orderId'])) {
                return array(
                    'status' => true,
                    'orderId' => $result['orderId'],
                    'state' => $result['state'] ?? 'PENDING', // PENDING, COMPLETED, FAILED
                    'amount' => $result['amount'] ?? null,
                    'payableAmount' => $result['payableAmount'] ?? null,
                    'feeAmount' => $result['feeAmount'] ?? null,
                    'expireAt' => $result['expireAt'] ?? null,
                    'metaInfo' => $result['metaInfo'] ?? null,
                    'paymentDetails' => $result['paymentDetails'] ?? array(),
                    'errorCode' => $result['errorCode'] ?? null, // Present when state is FAILED
                    'detailedErrorCode' => $result['detailedErrorCode'] ?? null, // Present when state is FAILED
                    'errorContext' => $result['errorContext'] ?? null, // Present when errorContext=true and state is FAILED
                    'merchantOrderId' => $merchantOrderId
                );
            }
        }
        
        // Handle non-200 responses
        $result = json_decode($response, true);
        return array(
            'status' => false,
            'message' => 'Failed to verify order status. HTTP Code: ' . $http_code,
            'response' => $response,
            'http_code' => $http_code,
            'error' => $result ?? null
        );
    }
}
?>
