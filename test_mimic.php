<?php
$mid = 'M1Y5YWMA86HR';
$salt_key = 'a187a974-55f2-4047-9c6e-a3b632743e45';
$salt_index = 1;
$url = 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay';

$con = new stdClass();
$con->merchantId = $mid;
$con->merchantTransactionId = "TXN" . time();
$con->merchantUserId = "U138";
$con->amount = 10000;
$con->redirectUrl = "https://doctto.com/callback";
$con->callbackUrl = "https://doctto.com/webhook";
$con->mobileNumber = "9999999999";
$con->paymentInstrument = new stdClass();
$con->paymentInstrument->type = "PAY_PAGE";

$encode = json_encode($con);
$encoded = base64_encode($encode);
$string = $encoded . "/pg/v1/pay" . $salt_key;
$sha256 = hash("sha256", $string);
$final_x_header = $sha256 . '###' . $salt_index;

$request_json_decode = new stdClass();
$request_json_decode->request = $encoded;
$request = json_encode($request_json_decode);

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $request,
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
