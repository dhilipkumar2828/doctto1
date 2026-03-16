<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PhonePeSDK {
    
    private $merchantId;
    private $saltKey;
    private $saltIndex;
    private $environment;
    private $baseUrl;
    
    public function __construct($config = array()) {
        $this->merchantId = $config['merchantId'] ?? 'M1Y5YWMA86HR';
        $this->saltKey = $config['saltKey'] ?? '168028f5-f3cf-40e3-a320-120926e1dcfb';
        $this->saltIndex = $config['saltIndex'] ?? 1;
        $this->environment = $config['environment'] ?? 'PRODUCTION';
        
        if ($this->environment === 'SANDBOX') {
            $this->baseUrl = 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        } else {
            $this->baseUrl = 'https://api.phonepe.com/apis/pg-sandbox';
        }
    }
    
    /**
     * Generate PhonePe payment request
     */
    public function generatePaymentRequest($data) {
        $payload = array(
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $data['merchantTransactionId'],
            'merchantUserId' => $data['merchantUserId'],
            'amount' => $data['amount'],
            'redirectUrl' => $data['redirectUrl'],
            'callbackUrl' => $data['callbackUrl'],
            'mobileNumber' => $data['mobileNumber'] ?? null,
            'paymentInstrument' => array('type' => 'PAY_PAGE')
        );
        
        $encode = json_encode($payload);
        $encoded = base64_encode($encode);
        $string = $encoded . '/pg/v1/pay' . $this->saltKey;
        $sha256 = hash('sha256', $string);
        $checksum = $sha256 . '###' . $this->saltIndex;
        
        return array(
            'request' => $encoded,
            'checksum' => $checksum,
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $data['merchantTransactionId'],
            'amount' => $data['amount'],
            'redirectUrl' => $data['redirectUrl'],
            'callbackUrl' => $data['callbackUrl']
        );
    }
    
    /**
     * Verify PhonePe callback response
     */
    public function verifyCallback($response) {
        $responseData = json_decode($response, true);
        
        if (!isset($responseData['response'])) {
            return false;
        }
        
        $decodedResponse = base64_decode($responseData['response']);
        $responseArray = json_decode($decodedResponse, true);
        
        if (!isset($responseArray['data'])) {
            return false;
        }
        
        // Verify checksum
        $checksum = $responseData['response'] . '/pg/v1/status/' . $this->merchantId . '/' . $responseArray['data']['merchantTransactionId'] . $this->saltKey;
        $sha256 = hash('sha256', $checksum);
        $expectedChecksum = $sha256 . '###' . $this->saltIndex;
        
        if ($responseData['checksum'] !== $expectedChecksum) {
            return false;
        }
        
        return $responseArray;
    }
    
    /**
     * Check payment status
     */
    public function checkPaymentStatus($merchantTransactionId) {
        $url = $this->baseUrl . '/pg/v1/status/' . $this->merchantId . '/' . $merchantTransactionId;
        
        $string = '/pg/v1/status/' . $this->merchantId . '/' . $merchantTransactionId . $this->saltKey;
        $sha256 = hash('sha256', $string);
        $checksum = $sha256 . '###' . $this->saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $checksum,
            'X-MERCHANT-ID: ' . $this->merchantId
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return $this->verifyCallback($response);
        }
        
        return false;
    }
    
    /**
     * Get SDK configuration for frontend
     */
    public function getSDKConfig() {
        return array(
            'merchantId' => $this->merchantId,
            'saltKey' => $this->saltKey,
            'saltIndex' => $this->saltIndex,
            'environment' => $this->environment,
            'baseUrl' => $this->baseUrl
        );
    }
}
