<?php
$url = "http://localhost/doctto1/admin/subscription_plans/manage_doctors";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $http_code\n\n";
echo $response;
