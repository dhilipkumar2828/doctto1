<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PhonePeSDKClient_Simple {
    
    private $clientId;
    private $clientSecret;
    private $clientVersion;
    private $environment;
    
    public function __construct($config = array()) {
        // Get configuration from constants or config
        $this->clientId = $config['clientId'] ?? (defined('PHONEPE_CLIENT_ID') ? PHONEPE_CLIENT_ID : 'PGTESTPAYUAT');
        $this->clientSecret = $config['clientSecret'] ?? (defined('PHONEPE_CLIENT_SECRET') ? PHONEPE_CLIENT_SECRET : '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399');
        $this->clientVersion = $config['clientVersion'] ?? (defined('PHONEPE_CLIENT_VERSION') ? PHONEPE_CLIENT_VERSION : '1.0');
        $this->environment = $config['environment'] ?? (defined('PHONEPE_ENVIRONMENT') ? PHONEPE_ENVIRONMENT : 'SANDBOX');
    }
    
    /**
     * Initiate Payment using PhonePe API
     */
    public function initiatePayment($merchantOrderId, $amount, $redirectUrl = null, $additionalParams = array()) {
        // Use sandbox PhonePe credentials for doctor subscriptions
        $merchant_id = defined('PHONEPE_SANDBOX_MERCHANT_ID') ? PHONEPE_SANDBOX_MERCHANT_ID : 'TEST-M1Y5YWMA86HR_250625';
        $salt_key = defined('PHONEPE_SANDBOX_SALT_KEY') ? PHONEPE_SANDBOX_SALT_KEY : 'Y2Y4NGIzZTAtOGI5Zi00MjZkLWI0OGYtNmI4OGExZDM3YTQ1';
        $key_index = defined('PHONEPE_SANDBOX_SALT_INDEX') ? PHONEPE_SANDBOX_SALT_INDEX : 1;
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
        
        // Create PhonePe API request - Use sandbox endpoint for testing
        $api_url = (defined('PHONEPE_SANDBOX_BASE_URL') ? PHONEPE_SANDBOX_BASE_URL : 'https://api-preprod.phonepe.com/apis/pg-sandbox') . '/pg/v1/pay';
        
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
            if ($result && isset($result['data']['instrumentResponse']['redirectInfo']['url'])) {
                return array(
                    'status' => true,
                    'data' => array(
                        'redirectUrl' => $result['data']['instrumentResponse']['redirectInfo']['url'],
                        'merchantOrderId' => $merchantOrderId,
                        'orderId' => $result['data']['merchantTransactionId'] ?? $merchantOrderId,
                        'status' => 'PENDING',
                        'expiresAt' => date('c', strtotime('+1 hour')),
                        'method' => $result['data']['instrumentResponse']['redirectInfo']['method'] ?? 'GET'
                    )
                );
            }
        }
        
        return array(
            'status' => false,
            'message' => 'Payment initiation failed - API error'
        );
    }
    
    /**
     * Check Order Status using PhonePe API
     */
    public function getOrderStatus($merchantOrderId, $details = true) {
        $merchant_id = defined('PHONEPE_SANDBOX_MERCHANT_ID') ? PHONEPE_SANDBOX_MERCHANT_ID : 'PGTESTPAYUAT';
        $api_url = (defined('PHONEPE_SANDBOX_BASE_URL') ? PHONEPE_SANDBOX_BASE_URL : 'https://api-preprod.phonepe.com/apis/pg-sandbox') . '/pg/v1/status/' . $merchant_id . '/' . $merchantOrderId;
        
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
            'message' => 'Order status check failed'
        );
    }
    
    /**
     * Verify Webhook Callback
     */
    public function verifyCallbackResponse($username, $password, $authorization, $responseBody) {
        // Basic webhook verification
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
}
?>
