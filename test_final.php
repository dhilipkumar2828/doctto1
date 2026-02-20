<?php
$mid = 'SU2510281348268224659678';
$salt_key = 'd8ff0940-f6ce-4cd4-8e68-d37639800639';
$url = 'https://api.phonepe.com/apis/hermes/pg/v1/pay';

$con = new stdClass();
$con->merchantId = $mid;
$con->merchantTransactionId = (string)time();
$con->merchantUserId = "138";
$con->amount = 10000;
$con->redirectUrl = "http://localhost/redirect";
$con->callbackUrl = "http://localhost/callback";
$con->mobileNumber = "9999999999";
$con->paymentInstrument = new stdClass();
$con->paymentInstrument->type = "PAY_PAGE";

$encode = json_encode($con);
$encoded = base64_encode($encode);
$string = $encoded . '/pg/v1/pay' . $salt_key;
$sha256 = hash("sha256", $string);
$final_x_header = $sha256 . '###1';

$request_json_decode = new stdClass();
$request_json_decode->request = $encoded;
$request = json_encode($request_json_decode);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $request,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-VERIFY: " . $final_x_header,
        "accept: application/json"
    ],
]);

$response = curl_exec($curl);
echo $response;
?>
