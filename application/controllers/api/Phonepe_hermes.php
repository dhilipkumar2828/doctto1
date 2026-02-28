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
     * Params: user_id, plan_id, type (doctor/customer), mobile
     */
    public function initiate_payment_post() {
        $user_id = $this->post('user_id');
        $plan_id = $this->post('plan_id');
        $type = $this->post('type'); // doctor / customer
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
            // 4. Initialize PhonePe V2 Client
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);

            // 5. Prepare Redirect & Callback URLs
            $redirect_url = base_url('api/phonepe_hermes/redirect?mtid=' . $merchant_transaction_id);

            // 6. Build Request Payload using SDK Builder
            $payRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchant_transaction_id)
                ->amount($amount_in_paise)
                ->message("Payment for {$type} subscription")
                ->redirectUrl($redirect_url)
                ->udf1((string)$user_id)
                ->udf2((string)$type)
                ->udf3((string)$plan_id)
                ->build();

            // 7. Call PhonePe Pay API
            $response = $client->pay($payRequest);

            // Update log with initiation response
            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payRequest),
                'response_payload' => json_encode($response)
            ]);

            if ($response && method_exists($response, 'getRedirectUrl') && $response->getRedirectUrl()) {
                $this->response([
                    'status' => 'success',
                    'message' => 'Payment initiated',
                    'payment_url' => $response->getRedirectUrl(),
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid response from PhonePe',
                    'details' => json_encode($response)
                ], REST_Controller::HTTP_OK);
            }

        } catch (Throwable $e) {
            $this->response([
                'status' => 'error',
                'message' => 'PhonePe V2 Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
        $this->initiate_payment_post();
    }

    /**
     * Verify Payment and get JSON Response
     * URL: api/phonepe_hermes/verify_payment?mtid=MTID123
     */
    public function verify_payment_get($mtid = null) {
        if (!$mtid) $mtid = $this->get('mtid');
        
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }

        // Fresh check from PhonePe status API
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
     * Redirect after payment (PhonePe sends user here)
     * URL: api/phonepe_hermes/redirect?mtid=MTID123
     */
    public function redirect_get() {
        $mtid = $this->get('mtid');
        $show_ui = $this->get('ui'); // Optional query param to show HTML UI

        if (!$mtid) {
            if (!$show_ui) {
                $this->response(['status' => 'error', 'message' => 'Invalid Transaction'], REST_Controller::HTTP_OK);
            } else {
                echo "<h1>Invalid Transaction</h1>";
            }
            return;
        }

        // Proactively verify status from PhonePe
        $this->verify_and_update_status($mtid);
        
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        // Default to JSON for App integration
        if (!$show_ui) {
            $this->verify_payment_get($mtid);
            return;
        }

        if ($log && $log->payment_status == 'success') {
            $app_redirect_url = "doctto://payment?status=success&mtid=" . $mtid;
            echo "<div style='text-align:center;margin-top:50px;font-family:sans-serif;'>
                    <h1 style='color:green;'>Payment Successful! ✅</h1>
                    <p>Transaction ID: $mtid</p>
                    <p>Your subscription has been activated.</p>
                    <p style='color:#666;'>Redirecting back to app in 3 seconds...</p>
                    <script>
                        setTimeout(function(){
                            window.location.href = '$app_redirect_url';
                        }, 3000);
                    </script>
                    <div style='margin-top:20px;'>
                        <a href='$app_redirect_url' style='padding:12px 25px;background:deeppink;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>Open App Now</a>
                    </div>
                  </div>";
        } else {
            $app_fail_url = "doctto://payment?status=failed&mtid=" . $mtid;
            echo "<div style='text-align:center;margin-top:50px;font-family:sans-serif;'>
                    <h1 style='color:red;'>Payment Pending or Failed ❌</h1>
                    <p>Transaction ID: $mtid</p>
                    <p>Status: " . ($log->payment_status ?? 'Unknown') . "</p>
                    <p>Redirecting back to app in 3 seconds...</p>
                    <script>
                        setTimeout(function(){
                            window.location.href = '$app_fail_url';
                        }, 3000);
                    </script>
                    <div style='margin-top:20px;'>
                        <a href='$app_fail_url' style='padding:12px 25px;background:#666;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>Back to App</a>
                    </div>
                  </div>";
        }
    }

    /**
     * Proactive status verification using V2 SDK
     */
    private function verify_and_update_status($mtid) {
        try {
            $clientId = PHONEPE_CLIENT_ID;
            $clientSecret = PHONEPE_CLIENT_SECRET;
            $clientVersion = PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);
            
            // Get status (withDetails = true)
            $res = $client->getOrderStatus($mtid, true);

            if ($res && $res->getState() == 'COMPLETED') {
                // Update DB
                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status' => 'success',
                    'response_payload' => json_encode($res)
                ]);
                
                // Activate Subscription
                $this->activate_user_subscription($mtid);
                return true;
            } else if ($res) {
                $status = 'failed';
                if ($res->getState() == 'PENDING') {
                    $status = 'pending';
                }
                
                $this->db->where('merchant_transaction_id', $mtid);
                $this->db->update('payment_logs', [
                    'payment_status' => $status,
                    'response_payload' => json_encode($res)
                ]);
            }
        } catch (Exception $e) {
            // Log error silently for this internal helper
            return false;
        }
        return false;
    }

    /**
     * Internal activation logic (Preserved from old version)
     */
    private function activate_user_subscription($mtid) {
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log && ($log->payment_status == 'success' || $log->payment_status == 'COMPLETED')) {
            // Double check if already activated in common_model or similar logic
            // (Assuming buy_subscription handles basic checking)

            $plan = $this->subscription_api_model->get_plan_details($log->plan_id);
            if ($plan) {
                $data = [
                    'type' => $log->type,
                    'plan_id' => $log->plan_id,
                    'duration' => $plan->duration_days,
                    'amount' => $log->amount,
                    'payment_id' => $mtid
                ];

                if ($log->type == 'doctor') {
                    $data['doctor_id'] = $log->user_id;
                } else {
                    $data['user_id'] = $log->user_id;
                }

                $this->subscription_api_model->buy_subscription($data);
            }
        }
    }

    /**
     * Standard status check API for frontend
     */
    public function status_post() {
        $mtid = $this->post('merchantTransactionId');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }

        // Sync with PhonePe
        $this->verify_and_update_status($mtid);

        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log) {
            $this->response([
                'status' => 'success',
                'payment_status' => $log->payment_status,
                'data' => $log
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Log not found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Webhook Callback from PhonePe (Optional for V2 Standard, but good to have)
     */
    public function callback_post() {
        // Implementation for V2 Webhook verification if needed
        // For now, we rely on proactive status check during redirect for better reliability in development
        $responseBody = file_get_contents('php://input');
        
        // Update log with webhook body
        $data = json_decode($responseBody);
        if (isset($data->orderId)) {
             $this->verify_and_update_status($data->orderId);
        }
        
        $this->output->set_status_header(200);
        echo json_encode(['status' => 'received']);
    }
}

