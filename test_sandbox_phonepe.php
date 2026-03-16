<?php
/**
 * Test PhonePe Sandbox Credentials for Doctor Subscriptions
 */

// Simulate CodeIgniter environment
define('BASEPATH', '');
define('APPPATH', 'application/');

// Load constants
require_once 'application/config/constants.php';

echo "🧪 TESTING PHONEPE SANDBOX CREDENTIALS\n";
echo "=====================================\n\n";

echo "1️⃣ Checking Sandbox Configuration...\n";
echo "✅ PHONEPE_CLIENT_ID: " . (defined('PHONEPE_CLIENT_ID') ? PHONEPE_CLIENT_ID : 'NOT DEFINED') . "\n";
echo "✅ PHONEPE_CLIENT_SECRET: " . (defined('PHONEPE_CLIENT_SECRET') ? PHONEPE_CLIENT_SECRET : 'NOT DEFINED') . "\n";
echo "✅ PHONEPE_ENVIRONMENT: " . (defined('PHONEPE_ENVIRONMENT') ? PHONEPE_ENVIRONMENT : 'NOT DEFINED') . "\n";
echo "✅ PHONEPE_SANDBOX_BASE_URL: " . (defined('PHONEPE_SANDBOX_BASE_URL') ? PHONEPE_SANDBOX_BASE_URL : 'NOT DEFINED') . "\n";
echo "✅ PHONEPE_SANDBOX_MERCHANT_ID: " . (defined('PHONEPE_SANDBOX_MERCHANT_ID') ? PHONEPE_SANDBOX_MERCHANT_ID : 'NOT DEFINED') . "\n";
echo "✅ PHONEPE_SANDBOX_SALT_KEY: " . (defined('PHONEPE_SANDBOX_SALT_KEY') ? PHONEPE_SANDBOX_SALT_KEY : 'NOT DEFINED') . "\n\n";

echo "2️⃣ Testing PhonePe Sandbox API Call...\n";

// Test parameters
$merchantOrderId = 'SUB_SANDBOX_' . time();
$amount = 49900; // 499.00 in paise
$doctor_id = 129;
$plan_id = 1;

// Use sandbox credentials
$merchant_id = PHONEPE_SANDBOX_MERCHANT_ID;
$salt_key = PHONEPE_SANDBOX_SALT_KEY;
$key_index = PHONEPE_SANDBOX_SALT_INDEX;
$base_url = PHONEPE_SANDBOX_BASE_URL;

echo "📋 Test Parameters:\n";
echo "   Merchant Order ID: $merchantOrderId\n";
echo "   Amount: $amount paise (₹" . ($amount/100) . ")\n";
echo "   Merchant ID: $merchant_id\n";
echo "   Base URL: $base_url\n\n";

// Create PhonePe request payload
$payload = array(
    'merchantId' => $merchant_id,
    'merchantTransactionId' => $merchantOrderId,
    'merchantUserId' => $doctor_id,
    'amount' => $amount,
    'redirectUrl' => 'https://doctto.com/api/doctors/subscription_payment_callback',
    'callbackUrl' => 'https://doctto.com/subscription_webhook/phonepe_subscription_webhook',
    'mobileNumber' => null,
    'paymentInstrument' => array('type' => 'PAY_PAGE')
);

echo "3️⃣ Creating Request Payload...\n";
$encode = json_encode($payload);
echo "✅ Payload JSON: " . $encode . "\n\n";

$encoded = base64_encode($encode);
echo "✅ Base64 Encoded: " . $encoded . "\n\n";

$string = $encoded . '/pg/v1/pay' . $salt_key;
$sha256 = hash('sha256', $string);
$final_x_header = $sha256 . '###' . $key_index;

echo "4️⃣ Creating Checksum...\n";
echo "✅ String to hash: " . $string . "\n";
echo "✅ SHA256: " . $sha256 . "\n";
echo "✅ Final X-Header: " . $final_x_header . "\n\n";

// Create PhonePe API request
$api_url = $base_url . '/pg/v1/pay';
echo "5️⃣ Making API Call...\n";
echo "✅ API URL: " . $api_url . "\n";

$request_data = json_encode(array('request' => $encoded));
echo "✅ Request Data: " . $request_data . "\n\n";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $api_url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'X-VERIFY: ' . $final_x_header,
    'accept: application/json'
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_VERBOSE, true);

echo "6️⃣ Executing cURL...\n";
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);

curl_close($curl);

echo "7️⃣ Response Analysis...\n";
echo "✅ HTTP Code: " . $http_code . "\n";
echo "✅ cURL Error: " . ($curl_error ?: 'None') . "\n";
echo "✅ Response: " . $response . "\n\n";

if ($http_code == 200) {
    $result = json_decode($response, true);
    if ($result && isset($result['data']['instrumentResponse']['redirectInfo']['url'])) {
        echo "🎉 SUCCESS! Sandbox payment initiated successfully\n";
        echo "✅ Redirect URL: " . $result['data']['instrumentResponse']['redirectInfo']['url'] . "\n";
        echo "✅ Transaction ID: " . $result['data']['merchantTransactionId'] . "\n";
        echo "✅ Method: " . $result['data']['instrumentResponse']['redirectInfo']['method'] . "\n";
        echo "✅ Environment: SANDBOX\n";
    } else {
        echo "❌ FAILED! Invalid response structure\n";
        echo "Response structure: " . print_r($result, true) . "\n";
    }
} else {
    echo "❌ FAILED! HTTP Error: " . $http_code . "\n";
    echo "Response: " . $response . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SANDBOX TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Using sandbox credentials for testing\n";
echo "✅ API call made to sandbox environment\n";
echo "📊 HTTP Code: " . $http_code . "\n";
echo "📊 Response Length: " . strlen($response) . " bytes\n";

if ($http_code == 200) {
    echo "🎉 Sandbox API call successful!\n";
    echo "✅ Doctor subscription sandbox testing ready!\n";
} else {
    echo "❌ Sandbox API call failed - check credentials\n";
}

echo "\n🚀 SANDBOX TESTING STEPS:\n";
echo "1. Test API endpoint: /api/doctors/initiate_subscription_payment\n";
echo "2. Use PhonePe Test App for payment testing\n";
echo "3. Verify webhook callbacks in sandbox\n";
echo "4. Test complete subscription flow\n\n";

echo "📱 SANDBOX API TEST COMMAND:\n";
echo "curl -X POST \"https://doctto.com/api/doctors/initiate_subscription_payment\" \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"doctor_id\": 129, \"plan_id\": 1, \"payment_method\": \"phonepe\"}'\n\n";

echo "📱 PHONEPE TEST APP:\n";
echo "1. Download PhonePe Test App from developer.phonepe.com\n";
echo "2. Use test credentials for payment testing\n";
echo "3. Test different payment scenarios\n\n";

echo "🎉 Sandbox testing setup completed!\n";
?>
