<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\Env;

/**
 * @property Subscription_api_model $subscription_api_model
 * @property Common_model $common_model
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Output $output
 */
class Phonepe_hermes extends REST_Controller {

    public function __construct() {
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        parent::__construct();
        $this->load->model('subscription_api_model');
        $this->load->model('common_model');
        $this->load->driver('cache', ['adapter' => 'file', 'backup' => 'dummy']); // ✅ Added
    }

    // -----------------------------------------------
    // Helper: Fetch PhonePe OAuth Access Token
    // -----------------------------------------------
    private function get_phonepe_access_token($clientId, $clientSecret) {

        // Check cache first
        $cached = $this->cache->get('phonepe_access_token');
        if ($cached) return $cached;

        $token_url = (PHONEPE_MODE == 'PROD') 
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token' 
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';

        $body = http_build_query([
            'client_id'      => $clientId,
            'client_version' => (int)PHONEPE_CLIENT_VERSION,
            'client_secret'  => $clientSecret,
            'grant_type'     => 'client_credentials',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => (PHONEPE_MODE == 'PROD') ? true : false,
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($http_code == 200) {
            $data = json_decode($response, true);
            
            // Handle both top-level and nested 'data' response
            $token = $data['access_token'] ?? ($data['data']['access_token'] ?? null);
            $expires_in = $data['expires_in'] ?? ($data['data']['expires_in'] ?? 1800);

            if ($token) {
                $this->cache->save('phonepe_access_token', $token, $expires_in - 120);
                return $token;
            }
        }

        log_message('error', 'PhonePe token fetch failed (HTTP ' . $http_code . '): ' . $response . ' CURL Error: ' . $curl_error);
        return null;
    }

    // -----------------------------------------------
    // Helper: Build cURL headers with O-Bearer token
    // -----------------------------------------------
    private function get_phonepe_curl_headers() {
        $clientId     = PHONEPE_CLIENT_ID;
        $clientSecret = PHONEPE_CLIENT_SECRET;

        $access_token = $this->get_phonepe_access_token($clientId, $clientSecret);

        if (!$access_token) return null;
        return [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $access_token,
            'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID,
        ];
    }

    /**
     * Step 1: Initiate Payment (V2 Standard Checkout)
     */
    public function initiate_payment_post() {

        $user_id = $this->post('user_id') ?? $this->get('user_id');
        $plan_id = $this->post('plan_id') ?? $this->get('plan_id');
        $type    = $this->post('type')    ?? $this->get('type');
        $mobile  = $this->post('mobile')  ?? $this->get('mobile');
        $autopay = $this->post('autopay') ?? $this->get('autopay');

        if (!$user_id || !$plan_id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing required fields'], REST_Controller::HTTP_OK);
            return;
        }

        if ($autopay == 1) {
            $this->initiate_autopay_setup_post();
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        if ($plan->plan_type != $type) {
            $this->response([
                'status'  => 'error',
                'message' => "This plan is for a {$plan->plan_type}, but request type is {$type}"
            ], REST_Controller::HTTP_OK);
            return;
        }

        $amount          = (float)$plan->price;
        $amount_in_paise = intval($amount * 100);
        $merchant_transaction_id = 'MTID' . time() . rand(100, 999);

        $this->db->insert('payment_logs', [
            'user_id'                 => $user_id,
            'plan_id'                 => $plan_id,
            'type'                    => $type,
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount'                  => $amount,
            'payment_status'          => 'pending',
            'provider'                => 'phonepe_v2',
            'created_at'              => date('Y-m-d H:i:s')
        ]);

        try {
            // ✅ Get O-Bearer headers
            $curlHeaders = $this->get_phonepe_curl_headers();
            if (!$curlHeaders) {
                $this->response(['status' => 'error', 'message' => 'Failed to get PhonePe access token'], REST_Controller::HTTP_OK);
                return;
            }

            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);

            $payload = [
                'merchantOrderId' => $merchant_transaction_id,
                'amount'          => (int)$amount_in_paise,
                'merchantUserId'  => 'MUID' . $user_id,
                'merchantUrls'    => [
                    'redirectUrl'       => $redirect_url,
                    'cancelRedirectUrl' => base_url('admin/doctor_subscription_plans')
                ],
                'paymentFlow'     => [
                    'type'         => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'subscriptionDetails' => [
                        'subscriptionType'       => 'RECURRING',
                        'merchantSubscriptionId' => 'SUB' . time(),
                        'authWorkflowType'       => 'TRANSACTION',
                        'amountType'             => 'FIXED',
                        'maxAmount'              => (int)$amount_in_paise,
                        'frequency'              => 'ON_DEMAND',
                        'productType'            => 'UPI_MANDATE',
                        'expireAt'               => (int)(strtotime('+1 day') * 1000)
                    ]
                ],
                'deviceContext' => [
                    'deviceOS' => 'ANDROID'
                ],
                'expireAfter' => 3000,
                'metaInfo' => [
                    'udf1' => (string)$user_id,
                    'udf2' => (string)$type,
                    'udf3' => (string)$plan_id
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => PHONEPE_BASE_URL . "/checkout/v2/pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => $curlHeaders,
                CURLOPT_TIMEOUT        => 30
            ]);

            $curl_response = curl_exec($ch);
            $http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response, true);

            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload'  => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data['redirectUrl'])) {
                $raw_payment_url = $response_data['redirectUrl'];
                $bridge_url = base_url('upi_intent_bridge.php')
                    . '?payment_url=' . urlencode($raw_payment_url)
                    . '&mtid=' . urlencode($merchant_transaction_id)
                    . '&return_url=' . urlencode($redirect_url);

                $this->response([
                    'status'                => 'success',
                    'message'               => 'Payment initiated',
                    'payment_url'           => $raw_payment_url,
                    'bridge_url'            => $bridge_url,
                    'redirect_url'          => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status'  => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        } catch (Throwable $e) {
            $this->response(['status' => 'error', 'message' => 'PhonePe Error: ' . $e->getMessage()], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Step 1b: Initiate AutoPay Setup (Subscription Mandate)
     */
    public function initiate_autopay_setup_post() {

        $user_id = $this->post('user_id') ?? $this->get('user_id');
        $plan_id = $this->post('plan_id') ?? $this->get('plan_id');
        $type    = $this->post('type')    ?? $this->get('type');
        $mobile  = $this->post('mobile')  ?? $this->get('mobile');

        if (!$user_id || !$plan_id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing required fields'], REST_Controller::HTTP_OK);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $amount          = (float)$plan->price;
        $amount_in_paise = intval($amount * 100);

        $duration = (int)$plan->duration_days;
        if ($duration == 1)                            $frequency = 'DAILY';
        elseif ($duration == 7)                        $frequency = 'WEEKLY';
        elseif ($duration >= 25 && $duration <= 35)   $frequency = 'MONTHLY';
        elseif ($duration >= 80 && $duration <= 100)  $frequency = 'QUARTERLY';
        elseif ($duration >= 170 && $duration <= 190) $frequency = 'HALF_YEARLY';
        elseif ($duration >= 360 && $duration <= 366) $frequency = 'YEARLY';
        else                                           $frequency = 'ON_DEMAND';

        $merchant_transaction_id  = 'MTID' . time() . rand(100, 999);
        $merchant_subscription_id = 'MSUB' . time() . rand(100, 999);
        $merchant_user_id         = 'MUID' . $user_id;

        $this->db->insert('payment_logs', [
            'user_id'                 => $user_id,
            'plan_id'                 => $plan_id,
            'type'                    => $type,
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount'                  => $amount,
            'payment_status'          => 'pending',
            'provider'                => 'phonepe_autopay',
            'created_at'              => date('Y-m-d H:i:s')
        ]);

        try {
            // ✅ Get O-Bearer headers
            $curlHeaders = $this->get_phonepe_curl_headers();
            if (!$curlHeaders) {
                $this->response(['status' => 'error', 'message' => 'Failed to get PhonePe access token'], REST_Controller::HTTP_OK);
                return;
            }

            $redirect_url   = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);
            $mandate_expiry = (time() + (10 * 365 * 24 * 60 * 60)) * 1000;
            $device_os      = strtoupper($this->post('device_os') ?? $this->get('device_os') ?? 'ANDROID');

            $payload = [
                'merchantOrderId' => $merchant_transaction_id,
                'amount'          => (int)$amount_in_paise,
                'merchantUserId'  => 'MUID' . $user_id,
                'merchantUrls'    => [
                    'redirectUrl'       => $redirect_url,
                    'cancelRedirectUrl' => base_url('admin/doctor_subscription_plans')
                ],
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'subscriptionDetails' => [
                        'subscriptionType'       => 'RECURRING',
                        'merchantSubscriptionId' => 'MSUB' . time(),
                        'authWorkflowType'       => 'TRANSACTION',
                        'amountType'             => 'FIXED',
                        'maxAmount'              => (int)$amount_in_paise,
                        'frequency'              => $frequency,
                        'productType'            => 'UPI_MANDATE',
                        'expireAt'               => (int)$mandate_expiry
                    ]
                ],
                'deviceContext' => [
                    'deviceOS' => 'ANDROID'
                ],
                'expireAfter' => 3000,
                'metaInfo' => [
                    'udf1' => (string)$user_id,
                    'udf2' => (string)$type,
                    'udf3' => (string)$plan_id,
                    'udf4' => 'AUTOPAY_SETUP'
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => PHONEPE_BASE_URL . '/checkout/v2/pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => $curlHeaders,
                CURLOPT_TIMEOUT        => 30
            ]);

            $curl_response = curl_exec($ch);
            $http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response, true);

            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload'  => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data['redirectUrl'])) {
                $raw_payment_url = $response_data['redirectUrl'];
                $bridge_url = base_url('upi_intent_bridge.php')
                    . '?payment_url=' . urlencode($raw_payment_url)
                    . '&mtid=' . urlencode($merchant_transaction_id)
                    . '&return_url=' . urlencode($redirect_url);

                $this->response([
                    'status'                  => 'success',
                    'message'                 => 'AutoPay setup initiated',
                    'payment_url'             => $raw_payment_url,
                    'bridge_url'              => $bridge_url,
                    'redirect_url'            => $redirect_url,
                    'merchantTransactionId'   => $merchant_transaction_id,
                    'merchantSubscriptionId'  => $merchant_subscription_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status'  => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        } catch (Throwable $e) {
            $this->response(['status' => 'error', 'message' => 'PhonePe AutoPay Error: ' . $e->getMessage()], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Legacy Alias for pay_get
     */
    public function pay_get() {
        $_POST['user_id'] = $this->get('user_id');
        $_POST['plan_id'] = $this->get('plan_id');
        $_POST['type']    = $this->get('type');
        $_POST['mobile']  = $this->get('mobile');
        $autopay = $this->get('autopay');

        if ($autopay == 1) {
            $this->initiate_autopay_setup_post();
        } else {
            $this->initiate_payment_post();
        }
    }

    /**
     * Verify Payment and get JSON Response
     */
    public function verify_payment_get($mtid = null) {
        if (!$mtid) $mtid = $this->get('mtid');

        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }

        $this->verify_and_update_status($mtid);

        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log) {
            $this->response([
                'status'                => 'success',
                'payment_status'        => $log->payment_status,
                'merchantTransactionId' => $mtid,
                'user_id'               => $log->user_id,
                'plan_id'               => $log->plan_id,
                'amount'                => $log->amount,
                'type'                  => $log->type
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Transaction not found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Proactive status verification using V2 SDK
     */
    private function verify_and_update_status($mtid) {
        try {
            $clientId      = (string)PHONEPE_CLIENT_ID;
            $clientSecret  = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString     = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            $res    = $client->getOrderStatus($mtid, true);

            if ($res && $res->getState() == 'COMPLETED') {
                $subscription_id = null;
                $res_array = json_decode(json_encode($res), true);
                if (isset($res_array['subscriptionDetails']['subscriptionId'])) {
                    $subscription_id = $res_array['subscriptionDetails']['subscriptionId'];
                }

                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status'   => 'success',
                    'response_payload' => json_encode($res)
                ]);

                $this->activate_user_subscription($mtid, $subscription_id);
                return true;

            } elseif ($res) {
                $status = ($res->getState() == 'PENDING') ? 'pending' : 'failed';
                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status'   => $status,
                    'response_payload' => json_encode($res)
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'verify_and_update_status error: ' . $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Internal activation logic with AutoPay support
     */
    private function activate_user_subscription($mtid, $autopay_agreement_id = null) {
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log && ($log->payment_status == 'success')) {

            if ($log->type == 'appointment') {
                $appointment_id = $log->plan_id;
                $this->db->where('id', $appointment_id);
                $this->db->update('online_doctor_appointments', [
                    'status'         => 'paid',
                    'payment_status' => 'COMPLETED'
                ]);

                $online_app = $this->db->where('id', $appointment_id)->get('online_doctor_appointments')->row();
                if ($online_app) {
                    $existing = $this->db
                        ->where('patient_id', $online_app->patient_id)
                        ->where('doctor_id', $online_app->doctor_id)
                        ->where('date', $online_app->date)
                        ->where('time_slot_value', $online_app->time_slot_value)
                        ->get('doctor_appointments')->row();

                    if (!$existing) {
                        $main_data = [
                            'patient_id'               => $online_app->patient_id,
                            'doctor_id'                => $online_app->doctor_id,
                            'date'                     => $online_app->date,
                            'time_slot_name'           => $online_app->time_slot_name,
                            'time_slot_value'          => $online_app->time_slot_value,
                            'patient_name'             => $online_app->patient_name,
                            'patient_mobile'           => $online_app->patient_mobile,
                            'patient_age'              => $online_app->patient_age,
                            'patient_gender'           => $online_app->patient_gender,
                            'patient_visiting_purpose' => $online_app->patient_visiting_purpose,
                            'consultation_fee'         => $online_app->consultation_fee,
                            'appointment_type'         => $online_app->type,
                            'doctor_status'            => 'active',
                            'created_date'             => $online_app->created_date
                        ];
                        $this->db->insert('doctor_appointments', $main_data);
                        $new_apt_id = $this->db->conn_id->insert_id;

                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', ['doctor_appointment_id' => $new_apt_id]);
                    } else {
                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', ['doctor_appointment_id' => $existing->id]);
                    }
                }
                return;
            }

            $plan = $this->subscription_api_model->get_plan_details($log->plan_id);
            if ($plan) {
                $data = [
                    'type'       => $log->type,
                    'plan_id'    => $log->plan_id,
                    'duration'   => $plan->duration_days,
                    'amount'     => $log->amount,
                    'payment_id' => $mtid
                ];

                if ($autopay_agreement_id) {
                    $data['autopay_agreement_id'] = $autopay_agreement_id;
                    $data['autopay_enabled']       = 1;
                    $data['payment_gateway']       = 'phonepe';
                }

                if ($log->type == 'doctor') {
                    $data['doctor_id']       = $log->user_id;
                    $data['featured_status'] = 1;
                } else {
                    $data['user_id'] = $log->user_id;
                }

                $sub = $this->subscription_api_model->buy_subscription($data);

                if ($sub && $autopay_agreement_id && $log->type == 'doctor') {
                    $this->db->where('id', $sub->id);
                    $this->db->update('doctor_subscriptions', [
                        'autopay_agreement_id' => $autopay_agreement_id,
                        'autopay_enabled'      => 1,
                        'payment_gateway'      => 'phonepe',
                        'featured_status'      => 1
                    ]);
                }
            }
        }
    }
    public function test_token_get() {
    $clientId     = PHONEPE_CLIENT_ID;
    $clientSecret = PHONEPE_CLIENT_SECRET;

    $token_url = (PHONEPE_MODE == 'PROD') 
        ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token' 
        : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';

    $body = http_build_query([
        'client_id'      => $clientId,
        'client_version' => (int)PHONEPE_CLIENT_VERSION,
        'client_secret'  => $clientSecret,
        'grant_type'     => 'client_credentials',
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $token_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => (PHONEPE_MODE == 'PROD') ? true : false,
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    $this->response([
        'status'       => 'success',
        'postman_headers' => [
            'Authorization'   => 'O-Bearer ' . (json_decode($response)->access_token ?? (json_decode($response)->data->access_token ?? 'FAILED')),
            'X-MERCHANT-ID'   => (string)PHONEPE_MERCHANT_ID,
            'Content-Type'    => 'application/json'
        ],
        'token_url'    => $token_url,
        'http_code'    => $http_code,
        'raw_response' => $response
    ], REST_Controller::HTTP_OK);
}

    public function status_post() {
        $mtid = $this->post('merchantTransactionId');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }
        $this->verify_and_update_status($mtid);
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();
        if ($log) {
            $this->response(['status' => 'success', 'payment_status' => $log->payment_status, 'data' => $log], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Log not found'], REST_Controller::HTTP_OK);
        }
    }

    public function callback_post() {
        $responseBody = file_get_contents('php://input');
        $data = json_decode($responseBody);
        if (isset($data->orderId)) {
            $this->verify_and_update_status($data->orderId);
        }
        $this->output->set_status_header(200);
        echo json_encode(['status' => 'received']);
    }

    public function redirect_get() {
        $mtid = $this->get('mtid');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'Invalid Transaction'], REST_Controller::HTTP_OK);
            return;
        }
        $this->verify_payment_get($mtid);
    }
}