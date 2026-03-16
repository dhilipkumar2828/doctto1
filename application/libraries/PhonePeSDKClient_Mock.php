<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PhonePeSDKClient_Mock {
    
    private $clientId;
    private $clientSecret;
    private $clientVersion;
    private $environment;
    
    public function __construct($config = array()) {
        // Get configuration from constants or config
        $this->clientId = $config['clientId'] ?? (defined('PHONEPE_CLIENT_ID') ? PHONEPE_CLIENT_ID : 'MOCK_CLIENT_ID');
        $this->clientSecret = $config['clientSecret'] ?? (defined('PHONEPE_CLIENT_SECRET') ? PHONEPE_CLIENT_SECRET : 'MOCK_CLIENT_SECRET');
        $this->clientVersion = $config['clientVersion'] ?? (defined('PHONEPE_CLIENT_VERSION') ? PHONEPE_CLIENT_VERSION : '1.0');
        $this->environment = $config['environment'] ?? (defined('PHONEPE_ENVIRONMENT') ? PHONEPE_ENVIRONMENT : 'SANDBOX');
    }
    
    /**
     * Mock Payment Initiation for Testing
     */
    public function initiatePayment($merchantOrderId, $amount, $redirectUrl = null, $additionalParams = array()) {
        // Mock successful payment initiation
        $mockRedirectUrl = 'https://mercury.phonepe.com/transact/mock?merchantId=MOCK_MERCHANT&merchantTransactionId=' . $merchantOrderId;
        
        return array(
            'status' => true,
            'data' => array(
                'redirectUrl' => $mockRedirectUrl,
                'merchantOrderId' => $merchantOrderId,
                'orderId' => 'MOCK_ORDER_' . time(),
                'status' => 'PENDING',
                'expiresAt' => date('c', strtotime('+1 hour')),
                'mock' => true,
                'message' => 'Mock payment initiated - use PhonePe Test App to complete'
            )
        );
    }
    
    /**
     * Mock Order Status Check
     */
    public function getOrderStatus($merchantOrderId, $details = true) {
        // Mock order status
        return array(
            'status' => true,
            'data' => array(
                'merchantOrderId' => $merchantOrderId,
                'orderId' => 'MOCK_ORDER_' . time(),
                'status' => 'PAYMENT_PENDING',
                'amount' => 49900,
                'paymentAttempts' => array(),
                'errorCode' => null,
                'errorMessage' => null,
                'mock' => true
            )
        );
    }
    
    /**
     * Mock Webhook Verification
     */
    public function verifyCallbackResponse($username, $password, $authorization, $responseBody) {
        // Mock webhook verification
        return array(
            'status' => true,
            'data' => array(
                'eventType' => 'PAYMENT',
                'merchantOrderId' => 'MOCK_ORDER_' . time(),
                'orderId' => 'MOCK_ORDER_' . time(),
                'status' => 'PAYMENT_SUCCESS',
                'amount' => 49900,
                'paymentAttempts' => array(),
                'errorCode' => null,
                'errorMessage' => null,
                'mock' => true
            )
        );
    }
}
?>
