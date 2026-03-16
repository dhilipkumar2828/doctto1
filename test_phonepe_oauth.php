<?php
require_once 'vendor/autoload.php';

use PhonePe\Env;
use PhonePe\common\configs\MerchantConfig;
use PhonePe\common\tokenHandler\TokenService;
use PhonePe\common\utils\CurlHttpClient;

// Constants from your app
$clientId = 'SU2510281348268224659678';
$clientSecret = 'd8ff0940-f6ce-4cd4-8e68-d37639800639';
$clientVersion = 1;

echo "Attempting to get OAuth token with:\n";
echo "Client ID: $clientId\n";
echo "Client Secret: $clientSecret\n\n";

try {
    $merchantConfig = new MerchantConfig($clientId, $clientVersion, $clientSecret);
    $httpClient = new CurlHttpClient();
    $tokenService = new TokenService($merchantConfig, Env::PRODUCTION, $httpClient);
    
    $headers = $tokenService->getAuthHeaders();
    echo "SUCCESS! Auth Headers generated:\n";
    print_r($headers);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "HTTP Status: " . (method_exists($e, 'getHttpStatusCode') ? $e->getHttpStatusCode() : 'N/A') . "\n";
}
?>
