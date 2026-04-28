<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
class Phonepe_hermes extends REST_Controller
{

    public function __construct()
    {
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
    private function get_phonepe_access_token($clientId, $clientSecret)
    {

        // Check cache first
        $cached = $this->cache->get('phonepe_access_token');
        if ($cached)
            return $cached;

        $token_url = (PHONEPE_MODE == 'PROD')
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';

        $body = http_build_query([
            'client_id' => $clientId,
            'client_version' => (int)PHONEPE_CLIENT_VERSION,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => (PHONEPE_MODE == 'PROD') ? true : false,
        ]);

        $response = curl_exec($ch);
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
    private function get_phonepe_curl_headers()
    {
        $clientId = PHONEPE_CLIENT_ID;
        $clientSecret = PHONEPE_CLIENT_SECRET;

        $access_token = $this->get_phonepe_access_token($clientId, $clientSecret);

        if (!$access_token)
            return null;
        return [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $access_token,
            // 'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID,
        ];
    }

    /**
     * Step 1: Initiate Payment (V2 Standard Checkout)
     */
    public function initiate_payment_post()
    {
        $user_id = $this->post('user_id') ?? $this->get('user_id');
        $plan_id = $this->post('plan_id') ?? $this->get('plan_id');
        $type = $this->post('type') ?? $this->get('type');
        $mobile = $this->post('mobile') ?? $this->get('mobile');
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
                'status' => 'error',
                'message' => "This plan is for a {$plan->plan_type}, but request type is {$type}"
            ], REST_Controller::HTTP_OK);
            return;
        }

        $amount = (float)$plan->price;
        $amount_in_paise = intval($amount * 100);
        $mtid = 'MTID' . time() . rand(100, 999);
        $merchant_sub_id = 'SUB' . time() . rand(100, 999);

        // Pre-calculate next billing date (1 day before expiry)
        $next_billing = date('Y-m-d', strtotime('+' . ($plan->duration_days - 1) . ' days'));

        $data = [
            'type' => $type,
            'plan_id' => $plan_id,
            'duration' => $plan->duration_days,
            'amount' => $amount,
            'payment_id' => $mtid,
            'payment_status' => 'pending',
            'payment_gateway' => 'phonepe',
            'auto_renew' => 1,
            'merchant_subscription_id' => $merchant_sub_id,
            'next_billing_date' => $next_billing
        ];

        if ($type == 'doctor') {
            $data['doctor_id'] = $user_id;
            $data['featured_status'] = 0;
        } else {
            $data['user_id'] = $user_id;
        }

        // Record it in our DB immediately
        $this->subscription_api_model->buy_subscription($data);

        $this->db->insert('payment_logs', [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'type' => $type,
            'merchant_transaction_id' => $mtid,
            'amount' => $amount,
            'payment_status' => 'pending',
            'provider' => 'phonepe_v2',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        try {
            // ✅ Get O-Bearer headers
            $curlHeaders = $this->get_phonepe_curl_headers();
            if (!$curlHeaders) {
                $this->response(['status' => 'error', 'message' => 'Failed to get PhonePe access token'], REST_Controller::HTTP_OK);
                return;
            }

            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $mtid);

            $payload = [
                'merchantOrderId' => $mtid,
                'amount' => (int)$amount_in_paise,
                'merchantUserId' => 'MUID' . $user_id,
                'merchantUrls' => [
                    'redirectUrl' => $redirect_url,
                    'cancelRedirectUrl' => base_url('admin/doctor_subscription_plans')
                ],
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'subscriptionDetails' => [
                        'subscriptionType' => 'RECURRING',
                        'merchantSubscriptionId' => $merchant_sub_id,
                        'authWorkflowType' => 'TRANSACTION',
                        'amountType' => 'FIXED',
                        'maxAmount' => (int)$amount_in_paise,
                        'frequency' => 'MONTHLY',
                        'productType' => 'UPI_MANDATE',
                        'expireAt' => (int)(strtotime('+1 day') * 1000)
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
                CURLOPT_URL => PHONEPE_BASE_URL . "/checkout/v2/pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $curlHeaders,
                CURLOPT_TIMEOUT => 30
            ]);

            $curl_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response, true);

            $this->db->where('merchant_transaction_id', $mtid);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data['redirectUrl'])) {
                $raw_payment_url = $response_data['redirectUrl'];
                $bridge_url = base_url('upi_intent_bridge.php')
                    . '?payment_url=' . urlencode($raw_payment_url)
                    . '&mtid=' . urlencode($mtid)
                    . '&return_url=' . urlencode($redirect_url);

                $this->response([
                    'status' => 'success',
                    'message' => 'Payment initiated',
                    'payment_url' => $raw_payment_url,
                    'bridge_url' => $bridge_url,
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $mtid
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        }
        catch (Throwable $e) {
            $this->response(['status' => 'error', 'message' => 'PhonePe Error: ' . $e->getMessage()], REST_Controller::HTTP_OK);
        }
    }

    public function initiate_autopay_setup_post()
    {
        $user_id = $this->post('user_id') ?? $this->get('user_id');
        $plan_id = $this->post('plan_id') ?? $this->get('plan_id');
        $type = $this->post('type') ?? $this->get('type');
        $mobile = $this->post('mobile') ?? $this->get('mobile');

        if (!$user_id || !$plan_id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing required fields'], REST_Controller::HTTP_OK);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        // For first time activation/mandate setup, charge only small amount or full price based on plan
        // Usually checkout setup flows require a small amount for mandate verification
        $amount = (float)($plan->price > 0 ? $plan->price : 2.00); 
        $amount_in_paise = intval($amount * 100);

        $duration = (int)$plan->duration_days;
        if ($duration == 1)
            $frequency = 'DAILY';
        elseif ($duration == 7)
            $frequency = 'WEEKLY';
        elseif ($duration >= 25 && $duration <= 35)
            $frequency = 'MONTHLY';
        elseif ($duration >= 80 && $duration <= 100)
            $frequency = 'QUARTERLY';
        elseif ($duration >= 170 && $duration <= 190)
            $frequency = 'HALF_YEARLY';
        elseif ($duration >= 360 && $duration <= 366)
            $frequency = 'YEARLY';
        else
            $frequency = 'ON_DEMAND';

        $mtid = 'MTID' . time() . rand(100, 999);
        $merchant_sub_id = 'MSUB' . time() . rand(100, 999);

        // Pre-calculate next billing date (1 day before expiry)
        $next_billing = date('Y-m-d', strtotime('+' . ($plan->duration_days - 1) . ' days'));

        $data = [
            'type' => $type,
            'plan_id' => $plan_id,
            'duration' => $plan->duration_days,
            'amount' => $amount,
            'payment_id' => $mtid,
            'payment_status' => 'pending',
            'payment_gateway' => 'phonepe',
            'auto_renew' => 1,
            'merchant_subscription_id' => $merchant_sub_id,
            'next_billing_date' => $next_billing,
            'autopay_enabled' => 1
        ];

        if ($type == 'doctor') {
            $data['doctor_id'] = $user_id;
            $data['featured_status'] = 0;
        } else {
            $data['user_id'] = $user_id;
        }

        // Record it in our DB immediately
        $this->subscription_api_model->buy_subscription($data);

        $this->db->insert('payment_logs', [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'type' => $type,
            'merchant_transaction_id' => $mtid,
            'amount' => $amount,
            'payment_status' => 'pending',
            'provider' => 'phonepe_autopay',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        try {
            // ✅ Get O-Bearer headers
            $curlHeaders = $this->get_phonepe_curl_headers();
            if (!$curlHeaders) {
                $this->response(['status' => 'error', 'message' => 'Failed to get PhonePe access token'], REST_Controller::HTTP_OK);
                return;
            }

            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $mtid);
            $mandate_expiry = (time() + (10 * 365 * 24 * 60 * 60)) * 1000;

            $payload = [
                'merchantOrderId' => $mtid,
                'amount' => (int)"200",
                'merchantUserId' => 'MUID' . $user_id,
                'merchantUrls' => [
                    'redirectUrl' => $redirect_url,
                ],
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'subscriptionDetails' => [
                        'subscriptionType' => 'RECURRING',
                        'merchantSubscriptionId' => $merchant_sub_id,
                        'authWorkflowType' => 'TRANSACTION',
                        'amountType' => 'VARIABLE',
                        'maxAmount' => (int)"99900", // Default high max for variable amounts
                        'frequency' => $frequency,
                        'productType' => 'UPI_MANDATE',
                        'expireAt' => (int)$mandate_expiry
                    ]
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
                CURLOPT_URL => PHONEPE_BASE_URL . '/checkout/v2/pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $curlHeaders,
                CURLOPT_TIMEOUT => 30
            ]);

            $curl_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response, true);

            $this->db->where('merchant_transaction_id', $mtid);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data['redirectUrl'])) {
                $raw_payment_url = $response_data['redirectUrl'];
                $bridge_url = base_url('upi_intent_bridge.php')
                    . '?payment_url=' . urlencode($raw_payment_url)
                    . '&mtid=' . urlencode($mtid)
                    . '&return_url=' . urlencode($redirect_url);

                $this->response([
                    'status' => 'success',
                    'message' => 'AutoPay setup initiated',
                    'payment_url' => $raw_payment_url,
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $mtid,
                    'merchantSubscriptionId' => $merchant_sub_id
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        }
        catch (Throwable $e) {
            $this->response(['status' => 'error', 'message' => 'PhonePe AutoPay Error: ' . $e->getMessage()], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Legacy Alias for pay_get
     */
    public function pay_get()
    {
        $_POST['user_id'] = $this->get('user_id');
        $_POST['plan_id'] = $this->get('plan_id');
        $_POST['type'] = $this->get('type');
        $_POST['mobile'] = $this->get('mobile');
        $autopay = $this->get('autopay');

        if ($autopay == 1) {
            $this->initiate_autopay_setup_post();
        }
        else {
            $this->initiate_payment_post();
        }
    }

    /**
     * Verify Payment and get JSON Response
     */
    public function verify_payment_get($mtid = null)
    {
        if (!$mtid)
            $mtid = $this->get('mtid');

        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }

        $this->verify_and_update_status($mtid);

        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log) {
            $this->response([
                'status' => 'success',
                'payment_status' => $log->payment_status,
                'merchantTransactionId' => $mtid,
                'user_id' => $log->user_id,
                'plan_id' => $log->plan_id,
                'amount' => $log->amount,
                'type' => $log->type
            ], REST_Controller::HTTP_OK);
        }
        else {
            $this->response(['status' => 'error', 'message' => 'Transaction not found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Proactive status verification using V2 SDK
     */
    private function verify_and_update_status($mtid)
    {
        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ?Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            $res = $client->getOrderStatus($mtid, true);

            if ($res && $res->getState() == 'COMPLETED') {
                $log = $this->db->where('merchant_transaction_id', $mtid)->get('payment_logs')->row();
                if ($log) {
                    $this->db->where('id', $log->id);
                    $this->db->update('payment_logs', [
                        'payment_status' => 'success',
                        'response_payload' => json_encode($res)
                    ]);

                    // Separate UMN and PhonePe Subscription ID
                    $umn_id = null;
                    $phonepe_sub_id = null;
                    $res_array = json_decode(json_encode($res), true);

                    if (isset($res_array['subscriptionDetails']['subscriptionId'])) {
                        $phonepe_sub_id = $res_array['subscriptionDetails']['subscriptionId'];
                    }

                    if (isset($res_array['paymentDetails'])) {
                        foreach ($res_array['paymentDetails'] as $pd) {
                            if (isset($pd['rail']['umn'])) {
                                $umn_id = $pd['rail']['umn'];
                                break;
                            }
                        }
                    }

                    $this->activate_user_subscription($log, $umn_id, $phonepe_sub_id);
                    return true;
                }

            }
            elseif ($res) {
                $status = ($res->getState() == 'PENDING') ? 'pending' : 'failed';
                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status' => $status,
                    'response_payload' => json_encode($res)
                ]);
            }
        }
        catch (Exception $e) {
            log_message('error', 'verify_and_update_status error: ' . $e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Internal activation logic with AutoPay support
     */
    private function activate_user_subscription($log, $autopay_agreement_id = null, $phonepe_subscription_id = null)
    {
        if ($log && ($log->payment_status == 'success')) {

            if ($log->type == 'appointment') {
                $appointment_id = $log->plan_id;
                $this->db->where('id', $appointment_id);
                $this->db->update('online_doctor_appointments', [
                    'status' => 'paid',
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
                            'patient_id' => $online_app->patient_id,
                            'doctor_id' => $online_app->doctor_id,
                            'date' => $online_app->date,
                            'time_slot_name' => $online_app->time_slot_name,
                            'time_slot_value' => $online_app->time_slot_value,
                            'patient_name' => $online_app->patient_name,
                            'patient_mobile' => $online_app->patient_mobile,
                            'patient_age' => $online_app->patient_age,
                            'patient_gender' => $online_app->patient_gender,
                            'patient_visiting_purpose' => $online_app->patient_visiting_purpose,
                            'consultation_fee' => $online_app->consultation_fee,
                            'appointment_type' => $online_app->type,
                            'doctor_status' => 'active',
                            'created_date' => $online_app->created_date
                        ];
                        $this->db->insert('doctor_appointments', $main_data);
                        $new_apt_id = $this->db->conn_id->insert_id;

                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', ['doctor_appointment_id' => $new_apt_id]);
                    }
                    else {
                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', ['doctor_appointment_id' => $existing->id]);
                    }
                }
                return;
            }

            $plan = $this->subscription_api_model->get_plan_details($log->plan_id);
            if ($plan) {
                // Calculate merchantSubscriptionId from request payload
                $payload_data = json_decode($log->request_payload, true);
                $merchant_subscription_id = $payload_data['paymentFlow']['subscriptionDetails']['merchantSubscriptionId'] ?? null;

                // Pre-calculate next billing date (1 day before expiry)
                $next_billing = date('Y-m-d', strtotime('+' . ($plan->duration_days - 1) . ' days'));

                $data = [
                    'type' => $log->type,
                    'plan_id' => $log->plan_id,
                    'duration' => $plan->duration_days,
                    'amount' => $log->amount,
                    'payment_id' => $log->merchant_transaction_id,
                    'payment_status' => 'success',
                    'payment_gateway' => 'phonepe',
                    'autopay_enabled' => ($autopay_agreement_id || $phonepe_subscription_id) ? 1 : 0,
                    'autopay_agreement_id' => $autopay_agreement_id,
                    'phonepe_subscription_id' => $phonepe_subscription_id,
                    'merchant_subscription_id' => $merchant_subscription_id,
                    'next_billing_date' => $next_billing
                ];

                if ($log->type == 'doctor') {
                    $data['doctor_id'] = $log->user_id;
                    $data['featured_status'] = 0;
                }
                else {
                    $data['user_id'] = $log->user_id;
                }

                $sub = $this->subscription_api_model->buy_subscription($data);

                // Handle already_active case by updating the existing subscription with new payment/autopay info
                if ($sub === 'already_active') {
                    $table = ($log->type == 'doctor') ? 'doctor_subscriptions' : 'user_subscriptions';
                    $id_field = ($log->type == 'doctor') ? 'doctor_id' : 'user_id';
                    
                    // Update existing active record with new payment/autopay details
                    $update_fields = [
                        'payment_id' => $log->merchant_transaction_id,
                        'amount' => $log->amount,
                        'payment_status' => 'success',
                        'payment_gateway' => 'phonepe',
                        'autopay_enabled' => $data['autopay_enabled'],
                        'autopay_agreement_id' => $autopay_agreement_id,
                        'phonepe_subscription_id' => $phonepe_subscription_id,
                        'merchant_subscription_id' => $merchant_subscription_id,
                        'next_billing_date' => $next_billing
                    ];
                    
                    if ($log->type == 'doctor') {
                        $update_fields['phonepe_agreement_id'] = $autopay_agreement_id; // Syncing
                    }

                    $this->db->where($id_field, $log->user_id);
                    $this->db->where('status', 'active');
                    $this->db->update($table, $update_fields);
                    
                    // Fetch the updated object
                    $sub = $this->db->get_where($table, [$id_field => $log->user_id, 'status' => 'active'])->row();
                }

                if (is_object($sub) && $log->type == 'doctor') {
                    // Create the initial payment success invoice
                    $this->db->insert('doctor_subscription_payments', [
                        'doctor_id' => $log->user_id,
                        'subscription_id' => $sub->id,
                        'payment_amount' => $log->amount,
                        'payment_method' => 'phonepe',
                        'payment_status' => 'success',
                        'is_renewal' => 0,
                        'autopay_setup' => $autopay_agreement_id ? 1 : 0,
                        'autopay_agreement_id' => $autopay_agreement_id,
                        'transaction_id' => $log->merchant_transaction_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // Schedule the future renewal visually in subscription_renewals
                    if ($autopay_agreement_id) {
                        $this->db->where('subscription_id', $sub->id);
                        $this->db->where('status', 'scheduled');
                        $exists = $this->db->get('subscription_renewals')->row();

                        if (!$exists) {
                            $renewal_data = [
                                'subscription_id' => $sub->id,
                                'doctor_id' => $log->user_id,
                                'renewal_date' => $sub->end_at,
                                'status' => 'scheduled',
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            $this->db->insert('subscription_renewals', $renewal_data);
                        }
                    }
                } elseif (is_object($sub) && $log->type != 'doctor') {
                    // Create the initial payment success invoice for User/Patient
                    $this->db->insert('user_subscription_payments', [
                        'user_id' => $log->user_id,
                        'subscription_id' => $sub->id,
                        'plan_id' => $log->plan_id,
                        'payment_amount' => $log->amount,
                        'payment_method' => 'phonepe',
                        'payment_status' => 'success',
                        'transaction_id' => $log->merchant_transaction_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // ✅ Call PhonePe Notify API for subscription setup sessions
                $this->notify_phonepe_subscription($log);
            }
        }
    }

    private function notify_phonepe_subscription($log)
    {
        $payload_data = json_decode($log->request_payload, true);
        $merchantSubscriptionId = null;

        if (isset($payload_data['paymentFlow']['subscriptionDetails']['merchantSubscriptionId'])) {
            $merchantSubscriptionId = $payload_data['paymentFlow']['subscriptionDetails']['merchantSubscriptionId'];
        }

        if (!$merchantSubscriptionId)
            return;

        $headers = $this->get_phonepe_curl_headers();
        if (!$headers)
            return;

        $notify_url = (PHONEPE_MODE == 'PROD')
            ? 'https://api.phonepe.com/apis/pg/checkout/v2/subscriptions/notify'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/subscriptions/notify';

        $notify_payload = [
            "merchantOrderId" => $log->merchant_transaction_id,
            "amount" => (int)($log->amount * 100),
            "paymentFlow" => [
                "type" => "SUBSCRIPTION_CHECKOUT_REDEMPTION",
                "merchantSubscriptionId" => $merchantSubscriptionId,
                "redemptionRetryStrategy" => "STANDARD",
                "autoDebit" => true
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $notify_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($notify_payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        log_message('debug', 'PhonePe Notify API Response for ' . $log->merchant_transaction_id . ': ' . $response);
    }
    public function test_token_get()
    {
        $clientId = PHONEPE_CLIENT_ID;
        $clientSecret = PHONEPE_CLIENT_SECRET;

        $token_url = (PHONEPE_MODE == 'PROD')
            ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';

        $body = http_build_query([
            'client_id' => $clientId,
            'client_version' => (int)PHONEPE_CLIENT_VERSION,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => (PHONEPE_MODE == 'PROD') ? true : false,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $this->response([
            'status' => 'success',
            'postman_headers' => [
                'Authorization' => 'O-Bearer ' . (json_decode($response)->access_token ?? (json_decode($response)->data->access_token ?? 'FAILED')),
                'X-MERCHANT-ID' => (string)PHONEPE_MERCHANT_ID,
                'Content-Type' => 'application/json'
            ],
            'token_url' => $token_url,
            'http_code' => $http_code,
            'raw_response' => $response
        ], REST_Controller::HTTP_OK);
    }

    public function status_post()
    {
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
        }
        else {
            $this->response(['status' => 'error', 'message' => 'Log not found'], REST_Controller::HTTP_OK);
        }
    }

    public function callback_post()
    {
        $responseBody = file_get_contents('php://input');
        $data = json_decode($responseBody);
        if (isset($data->orderId)) {
            $this->verify_and_update_status($data->orderId);
        }
        $this->output->set_status_header(200);
        echo json_encode(['status' => 'received']);
    }

    public function redirect_get()
    {
        $mtid = $this->get('mtid');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'Invalid Transaction'], REST_Controller::HTTP_OK);
            return;
        }
        $this->verify_payment_get($mtid);
    }

    /**
     * Process recurring payments for active auto-pay subscriptions
     * This should be called by a cron job (e.g., daily)
     */
    public function process_recurring_payments_get()
    {
        log_message('info', 'PhonePe: Starting recurring payments processing');

        // Find doctor subscriptions due for billing
        $this->db->select('ds.*, sp.price as plan_price, sp.duration_days');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('subscription_plans sp', 'sp.id = ds.doctor_subscription_plan_id');
        $this->db->where('ds.autopay_enabled', 1);
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.payment_gateway', 'phonepe');
        $this->db->where('ds.next_billing_date <=', date('Y-m-d'));
        $this->db->where('ds.merchant_subscription_id !=', NULL);
        
        $subscriptions = $this->db->get()->result();

        $results = [];
        foreach ($subscriptions as $sub) {
            $sub->sub_type = 'doctor';
            $results[] = $this->process_single_renewal($sub);
        }

        // Find user subscriptions due for billing
        $this->db->select('us.*, sp.price as plan_price, sp.duration_days');
        $this->db->from('user_subscriptions us');
        $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
        $this->db->where('us.autopay_enabled', 1);
        $this->db->where('us.status', 'active');
        $this->db->where('us.payment_gateway', 'phonepe');
        $this->db->where('us.next_billing_date <=', date('Y-m-d'));
        $this->db->where('us.merchant_subscription_id !=', NULL);
        
        $user_subscriptions = $this->db->get()->result();

        foreach ($user_subscriptions as $sub) {
            $sub->sub_type = 'user';
            $sub->doctor_id = $sub->user_id; // Aliasing for compatibility in process_single_renewal
            $sub->doctor_subscription_plan_id = $sub->plan_id; // Aliasing
            $results[] = $this->process_single_renewal($sub);
        }

        $this->response([
            'status' => 'success',
            'processed_count' => count($subscriptions) + count($user_subscriptions),
            'results' => $results
        ], REST_Controller::HTTP_OK);
    }

    private function process_single_renewal($sub)
    {
        $merchant_transaction_id = 'REN' . time() . rand(100, 999);
        $amount_in_paise = intval($sub->plan_price * 100);
        $sub_type = isset($sub->sub_type) ? $sub->sub_type : 'doctor';

        // 1. Log the renewal attempt
        $log_data = [
            'user_id' => $sub->doctor_id,
            'plan_id' => $sub->doctor_subscription_plan_id,
            'type' => $sub_type,
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount' => $sub->plan_price,
            'payment_status' => 'pending',
            'provider' => 'phonepe_autopay_renewal',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payment_logs', $log_data);

        // 2. Prepare Notify API (Redemption) payload
        $notify_payload = [
            "merchantOrderId" => $merchant_transaction_id,
            "amount" => $amount_in_paise,
            "paymentFlow" => [
                "type" => "SUBSCRIPTION_CHECKOUT_REDEMPTION",
                "merchantSubscriptionId" => $sub->merchant_subscription_id,
                "redemptionRetryStrategy" => "STANDARD",
                "autoDebit" => true
            ]
        ];

        $headers = $this->get_phonepe_curl_headers();
        $notify_url = (PHONEPE_MODE == 'PROD')
            ? 'https://api.phonepe.com/apis/pg/checkout/v2/subscriptions/notify'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/subscriptions/notify';

        // 3. Call PhonePe Notify API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $notify_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($notify_payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res_data = json_decode($response, true);

        // 4. Handle Response
        if ($http_code == 200 && isset($res_data['state']) && ($res_data['state'] == 'COMPLETED' || $res_data['state'] == 'SUCCESS')) {
            // Success: Update subscription dates
            $new_end_at = date('Y-m-d H:i:s', strtotime($sub->end_at . ' + ' . $sub->duration_days . ' days'));
            
            // Re-calculate next billing (1 day before new expiry)
            $new_next_billing = date('Y-m-d', strtotime($new_end_at . ' -1 day'));

            $this->db->where('id', $sub->id);
            $this->db->update('doctor_subscriptions', [
                'end_at' => $new_end_at,
                'next_billing_date' => $new_next_billing,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'payment_status' => 'success',
                'response_payload' => $response
            ]);

            return ['subscription_id' => $sub->id, 'status' => 'success', 'transaction_id' => $merchant_transaction_id];
        } else {
            // Failed
            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'payment_status' => 'failed',
                'response_payload' => $response
            ]);

            log_message('error', "PhonePe: Recurring payment failed for sub {$sub->id}: " . $response);
            return ['subscription_id' => $sub->id, 'status' => 'failed', 'error' => $response];
        }
    }
}