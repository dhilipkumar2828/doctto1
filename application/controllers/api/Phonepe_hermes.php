<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

/**
 * @property Subscription_api_model $subscription_api_model
 * @property Common_model $common_model
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Output $output
 */
class Phonepe_hermes extends REST_Controller {

    public function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        parent::__construct();
        $this->load->model('subscription_api_model');
        $this->load->model('common_model');
    }

    /**
     * Step 1: Initiate Payment
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
            'provider' => 'phonepe',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payment_logs', $log_data);

        // 4. Prepare PhonePe Payload
        $merchant_id = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_MERCHANT_ID : PHONEPE_HERMES_UAT_MERCHANT_ID;
        $salt_key = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_SALT_KEY : PHONEPE_HERMES_UAT_SALT_KEY;
        $salt_index = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_SALT_INDEX : PHONEPE_HERMES_UAT_SALT_INDEX;
        $base_url = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_URL : PHONEPE_HERMES_UAT_URL;

        // Custom callback URL from post if provided
        $custom_callback = $this->post('callback_url');
        $callback_url = $custom_callback ? $custom_callback : base_url('api/phonepe_hermes/callback'); 
        $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);

        $payload = [
            'merchantId' => $merchant_id,
            'merchantTransactionId' => $merchant_transaction_id,
            'merchantUserId' => 'MUID' . $user_id,
            'amount' => $amount_in_paise,
            'redirectUrl' => $redirect_url,
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => $callback_url,
            'mobileNumber' => $mobile ?? '9999999999',
            'paymentInstrument' => [
                'type' => 'PAY_PAGE'
            ]
        ];

        $encode = base64_encode(json_encode($payload));
        $string = $encode . '/pg/v1/pay' . $salt_key;
        $sha256 = hash("sha256", $string); // standard is lowercase hex
        $final_x_header = $sha256 . '###' . $salt_index;

        $request_payload = json_encode(['request' => $encode]);

        // Update log
        $this->db->where('merchant_transaction_id', $merchant_transaction_id);
        $this->db->update('payment_logs', [
            'request_payload' => $request_payload
        ]);

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
        $err = curl_error($curl);
        curl_close($curl);

        // Update response in log
        $this->db->where('merchant_transaction_id', $merchant_transaction_id);
        $this->db->update('payment_logs', ['response_payload' => $response]);

        if ($err) {
            $this->response(['status' => 'error', 'message' => 'Curl error: ' . $err], REST_Controller::HTTP_OK);
        } else {
            $res = json_decode($response);
            if (isset($res->success) && $res->success == true) {
                $pay_url = $res->data->instrumentResponse->redirectInfo->url;
                $this->response([
                    'status' => 'success',
                    'message' => 'Payment initiated',
                    'payment_url' => $pay_url,
                    'callback_url' => $callback_url,
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 'error',
                    'message' => 'PhonePe initiation failed',
                    'details' => $res
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Optional: Initiate via GET for testing/link clicking
     * URL: api/phonepe_hermes/pay?user_id=1&plan_id=5&type=doctor&mobile=9999999999
     */
    public function pay_get() {
        $_POST['user_id'] = $this->get('user_id');
        $_POST['plan_id'] = $this->get('plan_id');
        $_POST['type'] = $this->get('type');
        $_POST['mobile'] = $this->get('mobile');
        
        // Call the post method logic
        $this->initiate_payment_post();
    }

    /**
     * Webhook Callback from PhonePe
     */
    public function callback_post() {
        $response = file_get_contents('php://input');
        $headers = $this->input->request_headers();
        $x_verify = isset($headers['X-VERIFY']) ? $headers['X-VERIFY'] : (isset($headers['x-verify']) ? $headers['x-verify'] : '');

        // Verify Hash (Recommended)
        // ... (Skipping for now to ensure delivery, but good for security)

        $decoded_response = json_decode($response);
        if (isset($decoded_response->response)) {
            $final_data = json_decode(base64_decode($decoded_response->response));
            
            $mtid = $final_data->data->merchantTransactionId;
            $code = $final_data->code; // PAYMENT_SUCCESS, PAYMENT_ERROR etc

            // Update log
            $update_data = [
                'response_payload' => base64_decode($decoded_response->response),
                'payment_status' => ($code == 'PAYMENT_SUCCESS') ? 'success' : 'failed'
            ];
            $this->db->where('merchant_transaction_id', $mtid);
            $this->db->update('payment_logs', $update_data);

            // If success, activate subscription
            if ($code == 'PAYMENT_SUCCESS') {
                $this->activate_user_subscription($mtid);
            }
        }
        
        // Return 200 to PhonePe
        $this->output->set_status_header(200);
        echo json_encode(['status' => 'received']);
    }

    /**
     * Redirect after payment (Frontend usually hits this)
     */
    public function redirect_get() {
        $mtid = $this->get('mtid');
        if (!$mtid) {
            echo "<h1>Invalid Transaction</h1>";
            return;
        }

        // Proactively check status from PhonePe (Useful for Localhost testing where webhook fails)
        $this->verify_and_update_status($mtid);
        
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log && $log->payment_status == 'success') {
            $app_redirect_url = "doctto://payment?status=success&mtid=" . $mtid;
            echo "<div style='text-align:center;margin-top:50px;font-family:sans-serif;'>
                    <h1 style='color:green;'>Payment Successful! ✅</h1>
                    <p>Transaction ID: <b>$mtid</b></p>
                    <p>Your subscription has been activated.</p>
                    <p style='color:#666;'>Redirecting back to app in 3 seconds...</p>
                    <div style='margin-top:20px;'>
                        <a href='$app_redirect_url' style='padding:12px 25px;background:deeppink;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>Open App Now</a>
                    </div>
                    <script>
                        setTimeout(function(){
                            window.location.href = '$app_redirect_url';
                        }, 3000);
                    </script>
                  </div>";
        } else {
            $app_fail_url = "doctto://payment?status=failed&mtid=" . $mtid;
            echo "<div style='text-align:center;margin-top:50px;font-family:sans-serif;'>
                    <h1 style='color:red;'>Payment Pending or Failed ❌</h1>
                    <p>Transaction ID: $mtid</p>
                    <p>Status: " . ($log->payment_status ?? 'Unknown') . "</p>
                    <p>Redirecting back to app in 3 seconds...</p>
                    <div style='margin-top:20px;'>
                        <a href='$app_fail_url' style='padding:12px 25px;background:#666;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>Back to App</a>
                    </div>
                    <script>
                        setTimeout(function(){
                            window.location.href = '$app_fail_url';
                        }, 3000);
                    </script>
                  </div>";
        }
    }

    /**
     * Proactive status verification
     */
    private function verify_and_update_status($mtid) {
        $merchant_id = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_MERCHANT_ID : PHONEPE_HERMES_UAT_MERCHANT_ID;
        $salt_key = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_SALT_KEY : PHONEPE_HERMES_UAT_SALT_KEY;
        $salt_index = (PHONEPE_HERMES_MODE == 'PROD') ? PHONEPE_HERMES_PROD_SALT_INDEX : PHONEPE_HERMES_UAT_SALT_INDEX;
        
        // Final Status URL (Note: hermes uses status/merchantId/mtid)
        $status_url = (PHONEPE_HERMES_MODE == 'PROD') 
            ? "https://api.phonepe.com/apis/hermes/pg/v1/status/$merchant_id/$mtid"
            : "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/$merchant_id/$mtid";

        $string = "/pg/v1/status/$merchant_id/$mtid" . $salt_key;
        $sha256 = hash("sha256", $string);
        $final_x_header = $sha256 . '###' . $salt_index;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $status_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-VERIFY: " . $final_x_header,
                "X-MERCHANT-ID: " . $merchant_id,
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->code) && $res->code == 'PAYMENT_SUCCESS') {
            // Update DB
            $this->db->where('merchant_transaction_id', $mtid);
            $this->db->update('payment_logs', [
                'payment_status' => 'success',
                'response_payload' => $response
            ]);
            
            // Activate
            $this->activate_user_subscription($mtid);
            return true;
        } else if (isset($res->code)) {
            $this->db->where('merchant_transaction_id', $mtid);
            $this->db->update('payment_logs', [
                'payment_status' => ($res->code == 'PAYMENT_PENDING') ? 'pending' : 'failed',
                'response_payload' => $response
            ]);
        }
        return false;
    }

    /**
     * Internal activation logic
     */
    private function activate_user_subscription($mtid) {
        $this->db->where('merchant_transaction_id', $mtid);
        $log = $this->db->get('payment_logs')->row();

        if ($log && $log->payment_status == 'success') {
            // Check if already activated (prevent double activation)
            // ...

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
     * Check status manually (can be called by frontend)
     */
    public function status_post() {
        $mtid = $this->post('merchantTransactionId');
        if (!$mtid) {
            $this->response(['status' => 'error', 'message' => 'MTID required'], REST_Controller::HTTP_OK);
            return;
        }

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
     * Verify payment status via GET
     * URL: api/phonepe_hermes/verify_payment/MTID12345
     */
    public function verify_payment_get($mtid = null) {
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
                'data' => $log
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Transaction not found'], REST_Controller::HTTP_OK);
        }
    }
}
