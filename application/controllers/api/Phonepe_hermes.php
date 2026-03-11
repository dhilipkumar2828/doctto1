<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
// Load the PhonePe SDK via Composer Autoloader
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
        // Required security headers as per PhonePe MOM
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Cross-Origin-Opener-Policy: same-origin');
        
        // CORS headers
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        parent::__construct();
        $this->load->model('subscription_api_model');
        $this->load->model('common_model');
    }

    /**
     * Step 1: Initiate Payment (V2 Standard Checkout)
     * Params: user_id, plan_id, type (doctor/customer), mobile, autopay (0/1)
     */
    public function initiate_payment_post() {
        $user_id = $this->post('user_id') ?? $this->get('user_id');
        $plan_id = $this->post('plan_id') ?? $this->get('plan_id');
        $type = $this->post('type') ?? $this->get('type'); // doctor / customer
        $mobile = $this->post('mobile') ?? $this->get('mobile');
        $autopay = $this->post('autopay') ?? $this->get('autopay');

        if (!$user_id || !$plan_id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing required fields'], REST_Controller::HTTP_OK);
            return;
        }

        // If AutoPay is requested, redirect to the AutoPay Setup handler
        if ($autopay == 1) {
            $this->initiate_autopay_setup_post();
            return;
        }

        // 1. Get Plan Details
        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        // VALIDATION: Check if Plan Type matches Request Type
        if ($plan->plan_type != $type) {
            $this->response([
                'status' => 'error', 
                'message' => "This plan is for a {$plan->plan_type}, but you are trying to purchase as a {$type}."
            ], REST_Controller::HTTP_OK);
            return;
        }

        $amount = (float)$plan->price;
        $amount_in_paise = intval($amount * 100);

        // 2. Generate Transaction ID
        $merchant_transaction_id = 'MTID' . time() . rand(100, 999);

        // 3. Log the attempt in payment_logs
        $log_data = [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'type' => $type,
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount' => $amount,
            'payment_status' => 'pending',
            'provider' => 'phonepe_v2',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payment_logs', $log_data);

        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);

            // 6. Build Request Payload matching PhonePe V2 Standard Checkout
            $mobile = $this->post('mobile') ?: $this->get('mobile');
            $payload = [
                'merchantOrderId' => $merchant_transaction_id,
                'amount' => $amount_in_paise,
                'mobileNumber' => $mobile ?: null,
                'callbackUrl' => base_url('api/phonepe_hermes/callback'),
                'paymentFlow' => [
                    'type' => 'PG_CHECKOUT',
                    'merchantUrls' => [
                        'redirectUrl' => $redirect_url,
                        'cancelRedirectUrl' => base_url('admin/doctor_subscription_plans')
                    ]
                ],
                'expireAfter' => 3000,
                'redirectUrl' => $redirect_url, // Some mobile handlers look for this at top level
                'metaInfo' => [
                    'udf1' => (string)$user_id,
                    'udf2' => (string)$type,
                    'udf3' => (string)$plan_id
                ]
            ];


            // 7. Call PhonePe Pay API using raw cURL for consistency and header control
            $headers = $client->getHeaders();
            $headers['X-MERCHANT-ID'] = PHONEPE_MERCHANT_ID; 
            
            $curlHeaders = [];
            foreach ($headers as $key => $val) {
                $curlHeaders[] = "$key: $val";
            }

            $api_url = PHONEPE_BASE_URL . '/checkout/v2/pay';
            
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $curl_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response);

            // Update log with initiation response
            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data->redirectUrl)) {
                $this->response([
                    'status' => 'success',
                    'message' => 'Payment initiated',
                    'payment_url' => $response_data->redirectUrl,
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        } catch (Throwable $e) {
            $this->response([
                'status' => 'error',
                'message' => 'PhonePe V2 Error: ' . $e->getMessage()
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Step 1b: Initiate AutoPay Setup (Subscription Mandate)
     * Params: user_id, plan_id, type (doctor), mobile
     */
    public function initiate_autopay_setup_post() {
        $user_id = $this->post('user_id');
        $plan_id = $this->post('plan_id');
        $type = $this->post('type'); // should be 'doctor' for now
        $mobile = $this->post('mobile');

        if (!$user_id || !$plan_id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing required fields'], REST_Controller::HTTP_OK);
            return;
        }

        // 1. Get Plan Details
        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $amount = (float)$plan->price;
        $amount_in_paise = intval($amount * 100);

        // Map duration to PhonePe frequency based on user requirements
        $duration = (int)$plan->duration_days;
        if ($duration == 1) $frequency = 'DAILY';
        else if ($duration == 7) $frequency = 'WEEKLY';
        else if ($duration >= 25 && $duration <= 35) $frequency = 'MONTHLY';
        else if ($duration >= 80 && $duration <= 100) $frequency = 'QUARTERLY';
        else if ($duration >= 170 && $duration <= 190) $frequency = 'HALF_YEARLY';
        else if ($duration >= 360 && $duration <= 366) $frequency = 'YEARLY';
        else $frequency = 'ON_DEMAND';

        $merchant_transaction_id = 'MTID' . time() . rand(100, 999);
        $merchant_subscription_id = 'MSUB' . time() . rand(100, 999);
        $merchant_user_id = 'MUID' . $user_id; // Unique merchant user ID for subscriptions

        // 3. Log the attempt
        $log_data = [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'type' => $type,
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount' => $amount,
            'payment_status' => 'pending',
            'provider' => 'phonepe_autopay',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payment_logs', $log_data);

        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);

            // 4. Build Subscription Setup Payload according to latest PhonePe V2 Standard Checkout Spec
            $mandate_expiry = (time() + (10 * 365 * 24 * 60 * 60)) * 1000; 

            // Standard payload structure as recommended by PhonePe support
            $payload = [
                'merchantOrderId' => $merchant_transaction_id,
                'merchantUserId' => $merchant_user_id, // Added required MUID
                'amount' => $amount_in_paise,
                'mobileNumber' => $mobile ?: null,
                'callbackUrl' => base_url('api/phonepe_hermes/callback'),
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'merchantUrls' => [
                        'redirectUrl' => $redirect_url,
                        'cancelRedirectUrl' => base_url('admin/doctor_subscription_plans')
                    ],
                    'subscriptionDetails' => [
                        'subscriptionType' => 'RECURRING',
                        'merchantSubscriptionId' => $merchant_subscription_id,
                        'authWorkflowType' => 'TRANSACTION',
                        'amountType' => 'FIXED',
                        'maxAmount' => $amount_in_paise, // MUST be equal to amount for FIXED type
                        'frequency' => $frequency,
                        'productType' => 'UPI_MANDATE',
                        'expireAt' => $mandate_expiry
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

            // Some WebView implementations look for redirectUrl at top level
            $payload['redirectUrl'] = $redirect_url;

            // If mobile trigger, add device context for intent flow
            if ($this->post('device_os') || $this->get('device_os')) {
                $payload['deviceContext'] = [
                    'deviceOS' => strtoupper($this->post('device_os') ?? $this->get('device_os') ?? 'ANDROID')
                ];
            }


            // 5. Use SDK token but perform a raw cURL call to have full control over headers and payload structure
            // This ensures we match Sneha's "sample Curl" exactly.
            $headers = $client->getHeaders();
            $headers['X-MERCHANT-ID'] = PHONEPE_MERCHANT_ID; 
            
            // Re-format headers for cURL (associative to indexed strings)
            $curlHeaders = [];
            foreach ($headers as $key => $val) {
                $curlHeaders[] = "$key: $val";
            }

            $api_url = PHONEPE_BASE_URL . '/checkout/v2/pay';
            
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $curl_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($curl_response);

            // Update log with initiation response
            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payload),
                'response_payload' => $curl_response
            ]);

            if ($http_code == 200 && isset($response_data->redirectUrl)) {
                $this->response([
                    'status' => 'success',
                    'message' => 'AutoPay setup initiated',
                    'payment_url' => $response_data->redirectUrl,
                    'redirecturl' => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id,
                    'merchantSubscriptionId' => $merchant_subscription_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 'error',
                    'message' => 'PhonePe API Error (HTTP ' . $http_code . ')',
                    'details' => $curl_response
                ], REST_Controller::HTTP_OK);
            }

        } catch (Throwable $e) {
            $this->response([
                'status' => 'error',
                'message' => 'PhonePe AutoPay Error: ' . $e->getMessage()
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Legacy Alias for pay_get
     */
    public function pay_get() {
        $_POST['user_id'] = $this->get('user_id');
        $_POST['plan_id'] = $this->get('plan_id');
        $_POST['type'] = $this->get('type');
        $_POST['mobile'] = $this->get('mobile');
        $autopay = $this->get('autopay');
        
        // Use AutoPay setup if explicitly requested, otherwise default to standard payment
        // to show all payment options (Net Banking, Cards, etc.)
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

        // Sync with PhonePe Status API
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
        } else {
            $this->response(['status' => 'error', 'message' => 'Transaction not found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Proactive status verification using V2 SDK
     */
    private function verify_and_update_status($mtid) {
        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            
            // Get order status with details=true to capture subscriptionId
            $res = $client->getOrderStatus($mtid, true);

            if ($res && $res->getState() == 'COMPLETED') {
                $subscription_id = null;
                // Capture mandate/subscriptionId from PhonePe response safely
                $res_array = json_decode(json_encode($res), true);
                if (isset($res_array['subscriptionDetails']['subscriptionId'])) {
                    $subscription_id = $res_array['subscriptionDetails']['subscriptionId'];
                }

                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status' => 'success',
                    'response_payload' => json_encode($res)
                ]);
                
                // Activate Subscription and link Mandate ID
                $this->activate_user_subscription($mtid, $subscription_id);
                return true;
            } else if ($res) {
                $status = ($res->getState() == 'PENDING') ? 'pending' : 'failed';
                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status' => $status,
                    'response_payload' => json_encode($res)
                ]);
            }
        } catch (Exception $e) {
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
            // Handle Appointment Bookings
            if ($log->type == 'appointment') {
                $appointment_id = $log->plan_id; // For appointments, plan_id stores appointment_id
                $this->db->where('id', $appointment_id);
                $this->db->update('online_doctor_appointments', array(
                    'status' => 'paid',
                    'payment_status' => 'COMPLETED'
                ));
                
                // Also insert into main doctor_appointments if required by system
                $online_app = $this->db->where('id', $appointment_id)->get('online_doctor_appointments')->row();
                if ($online_app) {
                    // Check if this online appointment already exists in doctor_appointments
                    $existing = $this->db
                        ->where('patient_id', $online_app->patient_id)
                        ->where('doctor_id', $online_app->doctor_id)
                        ->where('date', $online_app->date)
                        ->where('time_slot_value', $online_app->time_slot_value)
                        ->get('doctor_appointments')->row();

                    if (!$existing) {
                        // Not found - safe to insert
                        $main_data = array(
                            'patient_id'              => $online_app->patient_id,
                            'doctor_id'               => $online_app->doctor_id,
                            'date'                    => $online_app->date,
                            'time_slot_name'          => $online_app->time_slot_name,
                            'time_slot_value'         => $online_app->time_slot_value,
                            'patient_name'            => $online_app->patient_name,
                            'patient_mobile'          => $online_app->patient_mobile,
                            'patient_age'             => $online_app->patient_age,
                            'patient_gender'          => $online_app->patient_gender,
                            'patient_visiting_purpose'=> $online_app->patient_visiting_purpose,
                            'consultation_fee'        => $online_app->consultation_fee,
                            'appointment_type'        => $online_app->type,
                            'doctor_status'           => 'active', // Wait for doctor to accept
                            'created_date'            => $online_app->created_date
                        );
                        $this->db->insert('doctor_appointments', $main_data);
                        $new_apt_id = $this->db->conn_id->insert_id;

                        // Link online appointment to the new doctor_appointments record
                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', array('doctor_appointment_id' => $new_apt_id));
                    } else {
                        // Already exists - just link the records
                        $this->db->where('id', $appointment_id);
                        $this->db->update('online_doctor_appointments', array('doctor_appointment_id' => $existing->id));
                    }
                }
                return;

            }

            // Handle Subscriptions (Doctor/Customer)
            $plan = $this->subscription_api_model->get_plan_details($log->plan_id);
            if ($plan) {

                $data = [
                    'type' => $log->type,
                    'plan_id' => $log->plan_id,
                    'duration' => $plan->duration_days,
                    'amount' => $log->amount,
                    'payment_id' => $mtid
                ];

                if ($autopay_agreement_id) {
                    $data['autopay_agreement_id'] = $autopay_agreement_id;
                    $data['autopay_enabled'] = 1;
                    $data['payment_gateway'] = 'phonepe';
                }

                if ($log->type == 'doctor') {
                    $data['doctor_id'] = $log->user_id;
                    $data['featured_status'] = 1; // Mark as featured if it's a doctor
                } else {
                    $data['user_id'] = $log->user_id;
                }

                $sub = $this->subscription_api_model->buy_subscription($data);
                
                // Ensure doctor_subscriptions record is updated with mandate details if successful
                if ($sub && $autopay_agreement_id && $log->type == 'doctor') {
                    $this->db->where('id', $sub->id);
                    $this->db->update('doctor_subscriptions', [
                        'autopay_agreement_id' => $autopay_agreement_id,
                        'autopay_enabled' => 1,
                        'payment_gateway' => 'phonepe',
                        'featured_status' => 1
                    ]);
                }
            }
        }
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
        $show_ui = $this->get('ui');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'Invalid Transaction'], REST_Controller::HTTP_OK);
            return;
        }
        $this->verify_payment_get($mtid);
    }
}
