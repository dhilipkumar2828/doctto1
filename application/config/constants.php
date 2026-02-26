<?php

defined('BASEPATH') OR exit('No direct script access allowed');  

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
// Get the directory path dynamically
$script_name = $_SERVER['SCRIPT_NAME'];
$dir = str_replace(basename($script_name), "", $script_name);
$url = $protocol . "://" . $host . $dir;

define('ADMIN_ASSETS_PATH', $url . "admin_assets/");
define('SHOP_LOGOS_PATH', $url . "uploads/shops/");
define('CATEGORIES_PATH', $url . "uploads/categories/");
define('SUB_CATEGORIES_PATH', $url . "uploads/sub_categories/");
define('DEFAULT_IMAGE_PATH', $url . "uploads/products/default.png");
define('PRODUCTS_PATH', $url . "uploads/products/default.png");
define('RAND', rand(1000,10000)); 

// PhonePe WebView Configuration (Legacy)
define("PAY_URL", "https://api.phonepe.com/apis/hermes/pg/v1/pay"); //for live
define("API_END_POINT", "/pg/v1/pay");
define("MERCHANT_ID", 'M1Y5YWMA86HR'); 
define("SALT_KEY", '168028f5-f3cf-40e3-a320-120926e1dcfb');
define("KEY_INDEX", 1);

// PhonePe SDK Configuration (Legacy - for WebView)
define("PHONEPE_SDK_MERCHANT_ID", 'M1Y5YWMA86HR'); 
define("PHONEPE_SDK_SALT_KEY", '168028f5-f3cf-40e3-a320-120926e1dcfb');
define("PHONEPE_SDK_SALT_INDEX", 1);
define("PHONEPE_SDK_ENVIRONMENT", 'PRODUCTION'); // or 'SANDBOX' for testing
define("PHONEPE_SDK_BASE_URL", 'https://api.phonepe.com/apis/pg-sandbox');   

// --- PhonePe Hermes (Standard Checkout) Integration ---

// UAT (Testing) Credentials
define("PHONEPE_HERMES_UAT_MERCHANT_ID", 'PGTESTPAYUAT86'); 
define("PHONEPE_HERMES_UAT_SALT_KEY", '96434309-7796-489d-8924-ab56988a6076');
define("PHONEPE_HERMES_UAT_SALT_INDEX", 1);
define("PHONEPE_HERMES_UAT_URL", 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay');

// PROD (Live) Credentials
define("PHONEPE_HERMES_PROD_MERCHANT_ID", 'SU2510281348268224659678');
define("PHONEPE_HERMES_PROD_SALT_KEY", 'd8ff0940-f6ce-4cd4-8e68-d37639800639');
define("PHONEPE_HERMES_PROD_SALT_INDEX", 1);
define("PHONEPE_HERMES_PROD_URL", 'https://api.phonepe.com/apis/hermes/pg/v1/pay');

// Current Mode
define("PHONEPE_HERMES_MODE", 'UAT'); // LIVE Production Mode - Real transactions

// PhonePe Production Configuration
define("PHONEPE_CLIENT_ID", 'M1Y5YWMA86HR'); // Production Client ID
define("PHONEPE_CLIENT_SECRET", '168028f5-f3cf-40e3-a320-120926e1dcfb'); // Production Client Secret
define("PHONEPE_CLIENT_VERSION", '1.0');
define("PHONEPE_ENVIRONMENT", 'PRODUCTION'); // Production environment

// PhonePe Production Configuration for Doctor Subscriptions
define("PHONEPE_BASE_URL", 'https://api.phonepe.com/apis/hermes');
define("PHONEPE_MERCHANT_ID", 'M1Y5YWMA86HR'); // Production Merchant ID
define("PHONEPE_SALT_KEY", '168028f5-f3cf-40e3-a320-120926e1dcfb'); // Production Salt Key
define("PHONEPE_SALT_INDEX", 1);

// PhonePe Webhook Configuration (Standard Checkout)
// These credentials must match the configuration in PhonePe Business Dashboard
define("PHONEPE_WEBHOOK_USERNAME", 'doctto_webhook_user'); // Webhook username (configured in PhonePe Dashboard)
define("PHONEPE_WEBHOOK_PASSWORD", 'Doctto2026'); // Webhook password (configured in PhonePe Dashboard)

