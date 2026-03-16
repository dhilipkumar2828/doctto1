<?php
define('BASEPATH', 'dummy');
require 'application/config/constants.php';

$merchant_id = PHONEPE_HERMES_UAT_MERCHANT_ID;
$salt_key = PHONEPE_HERMES_UAT_SALT_KEY;
$salt_index = PHONEPE_HERMES_UAT_SALT_INDEX;
$base_url = 'https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/pay';

$merchant_transaction_id = 'MTID' . time();

$payload = [
    'merchantId' => $merchant_id,
    'merchantTransactionId' => $merchant_transaction_id,
    'merchantUserId' => 'MUID138',
    'amount' => 100,
    'redirectUrl' => 'http://localhost/redirect',
    'redirectMode' => 'REDIRECT',
    'callbackUrl' => 'http://localhost/callback',
    'mobileNumber' => '9999999999',
    'paymentInstrument' => [
        'type' => 'PAY_PAGE'
    ]
];

$encode = base64_encode(json_encode($payload));
$string = $encode . '/pg/v1/pay' . $salt_key;
echo "String to hash: " . $string . "\n";
$sha256 = hash("sha256", $string);
$final_x_header = $sha256 . '###' . $salt_index;
echo "X-VERIFY: " . $final_x_header . "\n";

$request_payload = json_encode(['request' => $encode]);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $base_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $request_payload,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-VERIFY: " . $final_x_header,
        "accept: application/json"
    ],
]);

$response = curl_exec($curl);
echo $response;
curl_close($curl);
?>
