<?php
define('BASEPATH', 'dummy');
require 'application/config/constants.php';

function test_phonepe($mid, $key, $url) {
    echo "Testing MID: $mid with URL: $url\n";
    $payload = [
        'merchantId' => $mid,
        'merchantTransactionId' => 'MTID' . time() . rand(10,99),
        'merchantUserId' => 'MUID138',
        'amount' => 1000,
        'redirectUrl' => 'http://localhost/redirect',
        'redirectMode' => 'REDIRECT',
        'callbackUrl' => 'http://localhost/callback',
        'mobileNumber' => '9999999999',
        'paymentInstrument' => ['type' => 'PAY_PAGE']
    ];

    $encode = base64_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
    $string = $encode . '/pg/v1/pay' . $key;
    $sha256 = hash("sha256", $string);
    $final_x_header = $sha256 . '###1';

    $request_payload = json_encode(['request' => $encode]);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $request_payload,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-VERIFY: " . $final_x_header,
            "accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    echo "Response: $response\n\n";
    curl_close($curl);
}

// 1. Generic Sandbox
test_phonepe('PGTESTPAYUAT', '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399', 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay');

// 2. User Sandbox (Short ID)
test_phonepe('M1Y5YWMA86HR', 'a187a974-55f2-4047-9c6e-a3b632743e45', 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay');

// 3. User Sandbox (Long ID)
test_phonepe('M1Y5YWMA86HR_26022011411', 'a187a974-55f2-4047-9c6e-a3b632743e45', 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay');

// 4. Old PROD (Long known to work)
test_phonepe('M1Y5YWMA86HR', '168028f5-f3cf-40e3-a320-120926e1dcfb', 'https://api.phonepe.com/apis/hermes/pg/v1/pay');
?>
