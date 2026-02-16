<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Check if PhonePe SDK is available
$sdk_available = false;
if (file_exists(APPPATH . 'vendor/autoload.php')) {
    require_once APPPATH . 'vendor/autoload.php';
    $sdk_available = class_exists('PhonePe\PaymentGateway\Client\StandardCheckoutClient');
}

// Use SDK classes if available, otherwise create fallback
if ($sdk_available) {
    // SDK classes will be used when available
}

class PhonePeSDKClient {
    
    private $client;
    private $clientId;
    private $clientSecret;
    private $clientVersion;
    private $environment;
    
    public function __construct($config = array()) {
        // Get configuration from constants or config
        $this->clientId = $config['clientId'] ?? PHONEPE_CLIENT_ID;
        $this->clientSecret = $config['clientSecret'] ?? PHONEPE_CLIENT_SECRET;
        $this->clientVersion = $config['clientVersion'] ?? PHONEPE_CLIENT_VERSION;
        $this->environment = $config['environment'] ?? PHONEPE_ENVIRONMENT;
        
        // Log configuration for debugging
        if (function_exists('log_message')) {
            log_message('info', 'PhonePe SDK Client Config: ' . json_encode([
                'clientId' => $this->clientId,
                'environment' => $this->environment,
                'version' => $this->clientVersion
            ]));
        } else {
            error_log('PhonePe SDK Client Config: ' . json_encode([
                'clientId' => $this->clientId,
                'environment' => $this->environment,
                'version' => $this->clientVersion
            ]));
        }
        
        $this->initializeClient();
    }
    
    /**
     * Initialize PhonePe SDK Client
     * Following official documentation: https://developer.phonepe.com/payment-gateway/backend-sdk/php-be-sdk/integration-steps
     */
    private function initializeClient() {
        if (!$sdk_available) {
            if (function_exists('log_message')) {
                log_message('info', 'PhonePe SDK not available, using fallback implementation');
            } else {
                error_log('PhonePe SDK not available, using fallback implementation');
            }
            $this->client = null;
            return;
        }
        
        try {
            $this->client = new StandardCheckoutClient(
                $this->clientId,
                $this->clientSecret,
                $this->clientVersion,
                $this->environment
            );
        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'PhonePe SDK Client initialization failed: ' . $e->getMessage());
            } else {
                error_log('PhonePe SDK Client initialization failed: ' . $e->getMessage());
            }
            throw new Exception('PhonePe SDK initialization failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Initiate Payment using PhonePe SDK
     * Following official documentation for payment initiation
     */
    public function initiatePayment($merchantOrderId, $amount, $redirectUrl = null, $additionalParams = array()) {
        // Fallback implementation when SDK is not available
        if (!$sdk_available || $this->client === null) {
            return $this->fallbackInitiatePayment($merchantOrderId, $amount, $redirectUrl, $additionalParams);
        }
        
        try {
            // Build payment request using SDK builder
            $payRequestBuilder = new StandardCheckoutPayRequestBuilder();
            
            // Set required parameters
            $payRequestBuilder->setMerchantOrderId($merchantOrderId);
            $payRequestBuilder->setAmount($amount);
            
            // Set optional redirect URL
            if ($redirectUrl) {
                $payRequestBuilder->setRedirectUrl($redirectUrl);
            }
            
            // Add additional parameters if provided
            if (!empty($additionalParams)) {
                foreach ($additionalParams as $key => $value) {
                    $payRequestBuilder->setAdditionalParam($key, $value);
                }
            }
            
            // Build the request
            $payRequest = $payRequestBuilder->build();
            
            // Execute payment
            $payResponse = $this->client->pay($payRequest);
            
            return array(
                'status' => true,
                'data' => array(
                    'redirectUrl' => $payResponse->getRedirectUrl(),
                    'merchantOrderId' => $payResponse->getMerchantOrderId(),
                    'orderId' => $payResponse->getOrderId(),
                    'status' => $payResponse->getStatus(),
                    'expiresAt' => $payResponse->getExpiresAt()
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'PhonePe payment initiation failed: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            );
        }
    }
    
    /**
     * Fallback payment initiation when SDK is not available
     */
    private function fallbackInitiatePayment($merchantOrderId, $amount, $redirectUrl = null, $additionalParams = array()) {
        // Use existing PhonePe integration logic
        $merchant_id = PHONEPE_SANDBOX_MERCHANT_ID;
        $salt_key = PHONEPE_SANDBOX_SALT_KEY;
        $key_index = PHONEPE_SANDBOX_SALT_INDEX;
        $amount_in_paise = intval($amount);
        
        // Create PhonePe request payload
        $payload = array(
            'merchantId' => $merchant_id,
            'merchantTransactionId' => $merchantOrderId,
            'merchantUserId' => $additionalParams['doctor_id'] ?? 'DOCTOR',
            'amount' => $amount_in_paise,
            'redirectUrl' => $redirectUrl ?: 'https://doctto.com/api/doctors/subscription_payment_callback',
            'callbackUrl' => 'https://doctto.com/subscription_webhook/phonepe_subscription_webhook',
            'mobileNumber' => null,
            'paymentInstrument' => array('type' => 'PAY_PAGE')
        );

        $encode = json_encode($payload);
        $encoded = base64_encode($encode);
        $string = $encoded . '/pg/v1/pay' . $salt_key;
        $sha256 = hash('sha256', $string);
        $final_x_header = $sha256 . '###' . $key_index;
        
        // Create PhonePe API request
        $api_url = PHONEPE_SANDBOX_BASE_URL . '/pg/v1/pay';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('request' => $encoded)));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $final_x_header,
            'accept: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            if ($result && isset($result['data']['redirectUrl'])) {
                return array(
                    'status' => true,
                    'data' => array(
                        'redirectUrl' => $result['data']['redirectUrl'],
                        'merchantOrderId' => $merchantOrderId,
                        'orderId' => $result['data']['transactionId'] ?? $merchantOrderId,
                        'status' => 'PENDING',
                        'expiresAt' => date('c', strtotime('+1 hour'))
                    )
                );
            }
        }
        
        return array(
            'status' => false,
            'message' => 'Payment initiation failed - fallback method'
        );
    }
    
    /**
     * Check Order Status using PhonePe SDK
     * Following official documentation for order status checking
     */
    public function getOrderStatus($merchantOrderId, $details = true) {
        // Fallback implementation when SDK is not available
        if (!$sdk_available || $this->client === null) {
            return $this->fallbackGetOrderStatus($merchantOrderId, $details);
        }
        
        try {
            // Build order status request
            $orderStatusRequestBuilder = new StandardCheckoutOrderStatusRequestBuilder();
            $orderStatusRequestBuilder->setMerchantOrderId($merchantOrderId);
            $orderStatusRequestBuilder->setDetails($details);
            
            // Build the request
            $orderStatusRequest = $orderStatusRequestBuilder->build();
            
            // Get order status
            $orderStatusResponse = $this->client->getOrderStatus($orderStatusRequest);
            
            return array(
                'status' => true,
                'data' => array(
                    'merchantOrderId' => $orderStatusResponse->getMerchantOrderId(),
                    'orderId' => $orderStatusResponse->getOrderId(),
                    'status' => $orderStatusResponse->getStatus(),
                    'amount' => $orderStatusResponse->getAmount(),
                    'paymentAttempts' => $orderStatusResponse->getPaymentAttempts(),
                    'errorCode' => $orderStatusResponse->getErrorCode(),
                    'errorMessage' => $orderStatusResponse->getErrorMessage()
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'PhonePe order status check failed: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Order status check failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            );
        }
    }
    
    /**
     * Fallback order status check when SDK is not available
     */
    private function fallbackGetOrderStatus($merchantOrderId, $details = true) {
        // Use existing PhonePe integration logic
        $merchant_id = PHONEPE_SANDBOX_MERCHANT_ID;
        $salt_key = PHONEPE_SANDBOX_SALT_KEY;
        $key_index = PHONEPE_SANDBOX_SALT_INDEX;
        
        $api_url = PHONEPE_SANDBOX_BASE_URL . '/pg/v1/status/' . $merchant_id . '/' . $merchantOrderId;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'accept: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            if ($result && isset($result['data'])) {
                return array(
                    'status' => true,
                    'data' => array(
                        'merchantOrderId' => $merchantOrderId,
                        'orderId' => $result['data']['transactionId'] ?? $merchantOrderId,
                        'status' => $result['data']['state'] ?? 'PENDING',
                        'amount' => $result['data']['amount'] ?? 0,
                        'paymentAttempts' => array(),
                        'errorCode' => $result['data']['responseCode'] ?? null,
                        'errorMessage' => $result['data']['responseCode'] ?? null
                    )
                );
            }
        }
        
        return array(
            'status' => false,
            'message' => 'Order status check failed - fallback method'
        );
    }
    
    /**
     * Initiate Refund using PhonePe SDK
     * Following official documentation for refund initiation
     */
    public function initiateRefund($merchantRefundId, $originalMerchantOrderId, $amount) {
        try {
            // Build refund request
            $refundRequestBuilder = new StandardCheckoutRefundRequestBuilder();
            $refundRequestBuilder->setMerchantRefundId($merchantRefundId);
            $refundRequestBuilder->setOriginalMerchantOrderId($originalMerchantOrderId);
            $refundRequestBuilder->setAmount($amount);
            
            // Build the request
            $refundRequest = $refundRequestBuilder->build();
            
            // Execute refund
            $refundResponse = $this->client->refund($refundRequest);
            
            return array(
                'status' => true,
                'data' => array(
                    'merchantRefundId' => $refundResponse->getMerchantRefundId(),
                    'refundId' => $refundResponse->getRefundId(),
                    'amount' => $refundResponse->getAmount(),
                    'status' => $refundResponse->getStatus()
                )
            );
            
        } catch (PhonePeException $e) {
            log_message('error', 'PhonePe refund initiation failed: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Refund initiation failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            );
        } catch (Exception $e) {
            log_message('error', 'PhonePe refund initiation error: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Refund initiation error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Get Refund Status using PhonePe SDK
     * Following official documentation for refund status checking
     */
    public function getRefundStatus($merchantRefundId) {
        try {
            // Build refund status request
            $refundStatusRequestBuilder = new StandardCheckoutRefundStatusRequestBuilder();
            $refundStatusRequestBuilder->setMerchantRefundId($merchantRefundId);
            
            // Build the request
            $refundStatusRequest = $refundStatusRequestBuilder->build();
            
            // Get refund status
            $refundStatusResponse = $this->client->getRefundStatus($refundStatusRequest);
            
            return array(
                'status' => true,
                'data' => array(
                    'merchantRefundId' => $refundStatusResponse->getMerchantRefundId(),
                    'refundId' => $refundStatusResponse->getRefundId(),
                    'status' => $refundStatusResponse->getStatus(),
                    'amount' => $refundStatusResponse->getAmount(),
                    'refundAttempts' => $refundStatusResponse->getRefundAttempts()
                )
            );
            
        } catch (PhonePeException $e) {
            log_message('error', 'PhonePe refund status check failed: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Refund status check failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            );
        } catch (Exception $e) {
            log_message('error', 'PhonePe refund status check error: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Refund status check error: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Verify Webhook Callback using PhonePe SDK
     * Following official documentation for webhook verification
     */
    public function verifyCallbackResponse($username, $password, $authorization, $responseBody) {
        // Fallback implementation when SDK is not available
        if (!$sdk_available || $this->client === null) {
            return $this->fallbackVerifyCallbackResponse($username, $password, $authorization, $responseBody);
        }
        
        try {
            $callbackResponse = StandardCheckoutCallbackResponse::verifyCallbackResponse(
                $username,
                $password,
                $authorization,
                $responseBody
            );
            
            return array(
                'status' => true,
                'data' => array(
                    'eventType' => $callbackResponse->getEventType(),
                    'merchantOrderId' => $callbackResponse->getMerchantOrderId(),
                    'orderId' => $callbackResponse->getOrderId(),
                    'status' => $callbackResponse->getStatus(),
                    'amount' => $callbackResponse->getAmount(),
                    'paymentAttempts' => $callbackResponse->getPaymentAttempts(),
                    'errorCode' => $callbackResponse->getErrorCode(),
                    'errorMessage' => $callbackResponse->getErrorMessage()
                )
            );
            
        } catch (Exception $e) {
            log_message('error', 'PhonePe webhook verification failed: ' . $e->getMessage());
            return array(
                'status' => false,
                'message' => 'Webhook verification failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            );
        }
    }
    
    /**
     * Fallback webhook verification when SDK is not available
     */
    private function fallbackVerifyCallbackResponse($username, $password, $authorization, $responseBody) {
        // Basic webhook verification without SDK
        $json_decode = json_decode($responseBody);
        
        if (!$json_decode || !isset($json_decode->response)) {
            return array(
                'status' => false,
                'message' => 'Invalid webhook payload'
            );
        }

        $decode = base64_decode($json_decode->response);
        $dec = json_decode($decode);
        
        if (!$dec || !isset($dec->data)) {
            return array(
                'status' => false,
                'message' => 'Invalid decoded response'
            );
        }
        
        return array(
            'status' => true,
            'data' => array(
                'eventType' => 'PAYMENT',
                'merchantOrderId' => $dec->data->merchantTransactionId ?? null,
                'orderId' => $dec->data->transactionId ?? null,
                'status' => $dec->data->state ?? 'PENDING',
                'amount' => $dec->data->amount ?? 0,
                'paymentAttempts' => array(),
                'errorCode' => $dec->data->responseCode ?? null,
                'errorMessage' => $dec->data->responseCode ?? null
            )
        );
    }
    
    /**
     * Get Client Instance
     */
    public function getClient() {
        return $this->client;
    }
}
