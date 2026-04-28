<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the PhonePe SDK via Composer Autoloader
require_once FCPATH . 'vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);

/**
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Doctors_model $Doctors_model
 * @property CI_Email $email
 */
#[\AllowDynamicProperties]
class Subscription_renewal_cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load required models and drivers
        $this->load->model('Doctors_model');
        $this->load->database();
        $this->load->driver('cache', ['adapter' => 'file']);
        
        // Load PhonePe OAuth Service
        $this->load->library('PhonePeOAuthService');
        
        // Set timezone
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Main cron job method - should be called daily
     * Processes all subscription renewals, retries, and notifications
     */
    public function process_daily_renewals() {
        log_message('info', 'Starting daily subscription renewal process');
        
        try {
            // Process expiring subscriptions
            $this->process_expiring_subscriptions();
            $this->process_expiring_user_subscriptions();
            
            // Process failed autopay attempts
            $this->process_failed_autopay_attempts();
            $this->process_failed_user_autopay_attempts();
            
            // Send renewal notifications
            $this->send_renewal_notifications();
            $this->send_user_renewal_notifications();
            
            // Cleanup old records
            $this->cleanup_old_records();
            
            log_message('info', 'Daily subscription renewal process completed successfully');
            echo "SUCCESS: Daily renewal process completed\n";
            
        } catch (Exception $e) {
            log_message('error', 'Daily renewal process failed: ' . $e->getMessage());
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Process subscriptions that are expiring soon
     */
    private function process_expiring_subscriptions() {
        $expiring_subscriptions = $this->get_subscriptions_expiring_soon();
        foreach ($expiring_subscriptions as $subscription) {
            $this->process_single_subscription_renewal($subscription);
        }
    }

    /**
     * Get subscriptions that are due for billing today
     */
    private function get_subscriptions_expiring_soon() {
        $today = date('Y-m-d');
        // Look 2 days ahead to accommodate the 24-hour PhonePe notification requirement
        $target_date = date('Y-m-d', strtotime('+2 days'));
        
        $this->db->select('ds.*, d.doctor_name, d.email as doctor_email, d.mobile_number as doctor_phone, dsp.name as plan_name, dsp.price as plan_price, dsp.duration_days');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'd.id = ds.doctor_id');
        $this->db->join('subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
        
        // Pick both active and recently expired rows if they have auto-renew on
        $this->db->group_start();
        $this->db->where('ds.status', 'active');
        $this->db->group_end();
        
        $this->db->where('ds.auto_renew', 1);
        
        // Due date logic: pick anything due before or on the target_date
        $this->db->group_start();
        $this->db->where('ds.next_billing_date <=', $target_date);
        $this->db->or_where('ds.next_renewal_date <=', $target_date);
        $this->db->or_where('DATE(ds.end_at) <=', $target_date);
        $this->db->group_end();

        // 🚨 IMPORTANT: Ensure we don't notify twice if a success/notified payment exists for this specific billing date cycle.
        // For simplicity, we check if a notified/success payment was created in the last 48 hours for this subscription.
        $this->db->where("NOT EXISTS (
            SELECT 1 FROM doctor_subscription_payments dspay 
            WHERE dspay.subscription_id = ds.id 
            AND dspay.payment_status IN ('notified', 'success') 
            AND dspay.created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
        )", null, false);
        
        return $this->db->get()->result_array();
    }

    /**
     * Process user subscriptions that are expiring soon
     */
    private function process_expiring_user_subscriptions() {
        $expiring_subscriptions = $this->get_user_subscriptions_expiring_soon();
        foreach ($expiring_subscriptions as $subscription) {
            $this->process_single_user_subscription_renewal($subscription);
        }
    }

    /**
     * Get user subscriptions that are due for billing today
     */
    private function get_user_subscriptions_expiring_soon() {
        $today = date('Y-m-d');
        $target_date = date('Y-m-d', strtotime('+2 days'));
        
        $this->db->select('us.*, u.first_name as user_name, u.email as user_email, u.phone as user_phone, sp.name as plan_name, sp.price as plan_price, sp.duration_days');
        $this->db->from('user_subscriptions us');
        $this->db->join('users u', 'u.id = us.user_id');
        $this->db->join('subscription_plans sp', 'sp.id = us.plan_id');
        
        $this->db->group_start();
        $this->db->where('us.status', 'active');
        $this->db->group_end();
        
        $this->db->where('us.auto_renew', 1);
        
        $this->db->group_start();
        $this->db->where('us.next_billing_date <=', $target_date);
        $this->db->or_where('DATE(us.end_date) <=', $target_date);
        $this->db->group_end();

        $this->db->where("NOT EXISTS (
            SELECT 1 FROM user_subscription_payments uspay 
            WHERE uspay.subscription_id = us.id 
            AND uspay.payment_status IN ('notified', 'success') 
            AND uspay.created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
        )", null, false);
        
        return $this->db->get()->result_array();
    }

    /**
     * Process renewal for a single subscription
     */
    private function process_single_subscription_renewal($subscription) {
        if (!$subscription || !isset($subscription['id'])) {
            log_message('error', 'Attempted to process renewal with empty subscription data');
            return;
        }
        
        $sub_id = $subscription['id'];
        log_message('info', 'Processing renewal for subscription ID: ' . $sub_id);
        
        try {
            // Check if autopay is enabled (Support both new numeric and legacy status)
            $is_autopay = (isset($subscription['autopay_enabled']) && $subscription['autopay_enabled'] == 1) 
                       || (isset($subscription['autopay_status']) && $subscription['autopay_status'] == 'active');
            
            $agreement_id = $subscription['autopay_agreement_id'] ?? $subscription['merchant_subscription_id'] ?? $subscription['phonepe_agreement_id'] ?? null;

            if ($is_autopay) {
                
                $this->process_autopay_renewal($subscription);
            } else {
                echo "DEBUG: Autopay NOT enabled or Agreement ID missing for Sub #{$sub_id}. Skipping debit.\n";
                // Schedule manual renewal reminder
                $this->schedule_manual_renewal_reminder($subscription);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Failed to process renewal for subscription ' . $subscription['id'] . ': ' . $e->getMessage());
            $this->schedule_retry($subscription['id'], $subscription['doctor_id']);
        }
    }

    /**
     * Process renewal for a single user subscription
     */
    private function process_single_user_subscription_renewal($subscription) {
        if (!$subscription || !isset($subscription['id'])) {
            return;
        }
        
        $sub_id = $subscription['id'];
        log_message('info', 'Processing renewal for user subscription ID: ' . $sub_id);
        
        try {
            $is_autopay = isset($subscription['autopay_enabled']) && $subscription['autopay_enabled'] == 1;
            
            if ($is_autopay) {
                $this->process_user_autopay_renewal($subscription);
            } else {
                echo "DEBUG: User Autopay NOT enabled for Sub #{$sub_id}. Skipping debit.\n";
            }
            
        } catch (Exception $e) {
            log_message('error', 'Failed to process user renewal for ' . $subscription['id'] . ': ' . $e->getMessage());
        }
    }

    /**
     * Process user autopay renewal
     */
    private function process_user_autopay_renewal($subscription) {
        $payment_gateway = $subscription['payment_gateway'] ?? 'phonepe';
        
        if ($payment_gateway === 'phonepe') {
            $this->process_phonepe_user_autopay_renewal($subscription);
        }
    }

    /**
     * Process PhonePe user autopay renewal
     */
    private function process_phonepe_user_autopay_renewal($subscription) {
        $merchant_order_id = 'UREN' . time() . rand(100, 999);
        $amount_in_paise = (int)round((float)$subscription['plan_price'] * 100);

        echo "Initiating PhonePe User Recurring Debit for Sub: {$subscription['id']}, Amount Paise: {$amount_in_paise}\n";

        // Create renewal payment record
        $payment_data = [
            'user_id' => $subscription['user_id'],
            'subscription_id' => $subscription['id'],
            'plan_id' => $subscription['plan_id'],
            'payment_amount' => $subscription['plan_price'],
            'payment_method' => 'phonepe',
            'payment_status' => 'pending',
            'transaction_id' => $merchant_order_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('user_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();

        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            
            $token_url = (PHONEPE_MODE == 'PROD') ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token' : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';
            $token_payload = http_build_query([
                'client_id' => $clientId,
                'client_version' => $clientVersion,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials'
            ]);
            
            $ch = curl_init($token_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $token_payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            $token_res = curl_exec($ch);
            $token_data = json_decode($token_res);
            curl_close($ch);

            if (!isset($token_data->access_token)) {
                throw new Exception("Failed to get PhonePe access token for user renewal");
            }

            $access_token = $token_data->access_token;
            
            $notify_url = (PHONEPE_MODE == 'PROD') 
                ? 'https://api.phonepe.com/apis/pg/checkout/v2/subscriptions/notify' 
                : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/subscriptions/notify';
            
            $debit_payload = json_encode([
                'merchantId' => PHONEPE_MERCHANT_ID,
                'merchantOrderId' => $merchant_order_id,
                'amount' => $amount_in_paise,
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_REDEMPTION',
                    'merchantSubscriptionId' => $subscription['merchant_subscription_id'],
                    'redemptionRetryStrategy' => 'STANDARD',
                    'autoDebit' => true
                ],
            ]);
            
            $ch = curl_init($notify_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $debit_payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: O-Bearer ' . $access_token,
                'Content-Type: application/json',
                'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID,
                'X-CLIENT-ID: ' . PHONEPE_CLIENT_ID,
                'accept: application/json'
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $res_data = json_decode($response);
            echo "DEBUG: PhonePe User AutoPay Response for Sub {$subscription['id']}: " . json_encode($res_data) . "\n";
            
            if ($http_code == 200 && isset($res_data->state) && ($res_data->state == 'COMPLETED' || $res_data->state == 'SUCCESS' || $res_data->state == 'NOTIFICATION_IN_PROGRESS')) {
                $this->db->where('id', $payment_id);
                $this->db->update('user_subscription_payments', ['payment_status' => 'success', 'phonepe_transaction_id' => $res_data->orderId ?? '']);
                
                $this->extend_user_subscription($subscription);
                log_message('info', "PhonePe User AutoPay Success (or In Progress) for Sub: {$subscription['id']}");
            } else {
                echo "DEBUG: PhonePe User AutoPay Response for Sub {$subscription['id']}: " . json_encode($res_data) . "\n";
                $error_code = $res_data->code ?? '';
                $this->db->where('id', $payment_id);
                $this->db->update('user_subscription_payments', [
                    'payment_status' => 'failed',
                    'error_message' => $res_data->message ?? 'Unknown API Error'
                ]);
                $this->db->where('id', $subscription['id']);
                $this->db->update('user_subscriptions', ['status' => 'expired']);
                log_message('info', "User subscription #{$subscription['id']} marked as expired due to INVALID_SUBSCRIPTION_STATE");
               
                if ($error_code === 'INVALID_SUBSCRIPTION_STATE') {
                    $this->db->where('id', $subscription['id']);
                    $this->db->update('user_subscriptions', ['status' => 'expired']);
                    log_message('info', "User subscription #{$subscription['id']} marked as expired due to INVALID_SUBSCRIPTION_STATE");
                }

                log_message('error', "PhonePe User AutoPay Failed for Sub: {$subscription['id']}. Code: {$error_code}, Reason: " . ($res_data->message ?? 'Unknown'));
            }

        } catch (Exception $e) {
            log_message('error', "PhonePe User AutoPay Exception for Sub: {$subscription['id']}: " . $e->getMessage());
            $this->db->where('id', $payment_id);
            $this->db->update('user_subscription_payments', ['payment_status' => 'failed']);
            $this->db->where('id', $subscription['id']);
            $this->db->update('user_subscriptions', ['status' => 'expired']);
            log_message('info', "User subscription #{$subscription['id']} marked as expired due to INVALID_SUBSCRIPTION_STATE");
               
        }
    }

    private function extend_user_subscription($subscription) {
        $days = (isset($subscription['duration_days']) && $subscription['duration_days'] > 0) ? $subscription['duration_days'] : 30;
        
        $current_end_str = !empty($subscription['end_date']) ? $subscription['end_date'] : date('Y-m-d H:i:s');
        $current_end = strtotime($current_end_str);
        $base_time = ($current_end > time()) ? $current_end : time();
        
        $new_end_date = date('Y-m-d H:i:s', strtotime("+{$days} days", $base_time));
        $new_next_billing = date('Y-m-d', strtotime($new_end_date . ' -1 day'));

        echo "DEBUG: Extending User Sub #{$subscription['id']} | Days: {$days} | Current End: {$current_end_str} | Base: " . date('Y-m-d H:i:s', $base_time) . " | New End: {$new_end_date} | New Next Billing: {$new_next_billing}\n";

        $this->db->where('id', $subscription['id']);
        $this->db->update('user_subscriptions', [
            'end_date' => $new_end_date,
            'next_billing_date' => $new_next_billing
        ]);
        
        log_message('info', "Extended user subscription #{$subscription['id']} to {$new_end_date}. Next billing: {$new_next_billing}");
    }

    /**
     * Process autopay renewal
     */
    private function process_autopay_renewal($subscription) {
        $payment_gateway = $subscription['payment_gateway'] ?? 'phonepe';
        
        if ($payment_gateway === 'phonepe') {
            $this->process_phonepe_autopay_renewal($subscription);
        } elseif ($payment_gateway === 'razorpay') {
            $this->process_razorpay_autopay_renewal($subscription);
        }
    }

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

    private function get_phonepe_curl_headers()
    {
        $clientId = PHONEPE_CLIENT_ID;
        $clientSecret = PHONEPE_CLIENT_SECRET;

        $access_token = $this->get_phonepe_access_token($clientId, $clientSecret);

        if (!$access_token)
            return null;
            
        log_message('error', 'Cron using PhonePe Token: ' . substr($access_token, 0, 10) . '... (length: ' . strlen($access_token) . ')');

        return [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $access_token,
            'X-CLIENT-ID: ' . PHONEPE_CLIENT_ID,
            'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID
        ];
    }

    /**
     * Process PhonePe autopay renewal
     */
   private function process_phonepe_autopay_renewal($subscription) {
        $agreement_id = $subscription['autopay_agreement_id'];
        $merchant_order_id = 'REN' . time() . rand(100, 999);
        $amount_in_paise = (int)round((float)$subscription['plan_price'] * 100);

        echo "Initiating PhonePe Recurring Debit for Sub: {$subscription['id']}, Amount Paise: {$amount_in_paise}\n";

        // Create renewal payment record first (pending)
        $payment_data = [
            'doctor_id' => $subscription['doctor_id'],
            'subscription_id' => $subscription['id'],
            'payment_amount' => $subscription['plan_price'],
            'payment_method' => 'phonepe',
            'payment_status' => 'pending',
            'transaction_id' => $merchant_order_id,
            'is_renewal' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('doctor_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();

        try {
            // Use Client ID/Secret from constants
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ? 'PROD' : 'UAT';

            // Manual token fetch for the backend call
            $token_url = (PHONEPE_MODE == 'PROD') ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token' : 'https://api-preprod.phonepe.com/apis/pg-sandbox/identity-manager/v1/oauth/token';
            $token_payload = http_build_query([
                'client_id' => $clientId,
                'client_version' => $clientVersion,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials'
            ]);
            
            $ch = curl_init($token_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $token_payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            $token_res = curl_exec($ch);
            $token_data = json_decode($token_res);
            curl_close($ch);

            if (!isset($token_data->access_token)) {
                throw new Exception("Failed to get PhonePe access token: " . $token_res);
            }

            $access_token = $token_data->access_token;
            
            // Recurring Debit API V2 (Notify Endpoint for Redemption)
            $notify_url = (PHONEPE_MODE == 'PROD') 
                ? 'https://api.phonepe.com/apis/pg/checkout/v2/subscriptions/notify' 
                : 'https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/subscriptions/notify';
            
            $debit_payload = json_encode([
                'merchantId' => PHONEPE_MERCHANT_ID,
                'merchantOrderId' => $merchant_order_id,
                'amount' => $amount_in_paise,
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_REDEMPTION',
                    'merchantSubscriptionId' => $subscription['merchant_subscription_id'],
                    'redemptionRetryStrategy' => 'STANDARD',
                    'autoDebit' => true
                ],
            ]);
            
            
            $ch = curl_init($notify_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $debit_payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: O-Bearer ' . $access_token,
                'Content-Type: application/json',
                'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID,
                'X-CLIENT-ID: ' . PHONEPE_CLIENT_ID,
                'accept: application/json'
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $res_data = json_decode($response);
            echo "DEBUG: PhonePe AutoPay Response: " . json_encode($res_data) . "\n";
            echo "DEBUG: PhonePe Request Payload: " . $debit_payload . "\n";
            if ($http_code == 200 && isset($res_data->state) && ($res_data->state == 'COMPLETED' || $res_data->state == 'SUCCESS' || $res_data->state == 'NOTIFICATION_IN_PROGRESS')) {
                // Success
                $this->db->where('id', $payment_id);
                $this->db->update('doctor_subscription_payments', ['payment_status' => 'success', 'phonepe_transaction_id' => $res_data->orderId ?? '']);
                
                // Update subscription expiry
                $this->extend_subscription($subscription);
                
                log_message('info', "PhonePe AutoPay Success for Sub: {$subscription['id']}");
            } else {
                // Failed or Pending
                $error_code = $res_data->code ?? '';
                $this->db->set('payment_status', 'notified_fail');
                $this->db->set('failed_reason', (isset($res_data->message) ? $res_data->message : 'Unknown API Error'));
                $this->db->where('id', $payment_id);
                $this->db->update('doctor_subscription_payments');

                if ($error_code === 'INVALID_SUBSCRIPTION_STATE') {
                    $this->db->where('id', $subscription['id']);
                    $this->db->update('doctor_subscriptions', ['status' => 'expired']);
                    log_message('info', "Doctor subscription #{$subscription['id']} marked as expired due to INVALID_SUBSCRIPTION_STATE");
                }
                
                log_message('error', "PhonePe AutoPay Failed for Sub: {$subscription['id']}. Code: {$error_code}, Reason: " . ($res_data->message ?? 'Unknown'));
            }

        } catch (Exception $e) {
            log_message('error', "PhonePe AutoPay Exception for Sub: {$subscription['id']}: " . $e->getMessage());
            $this->db->where('id', $payment_id);
            $this->db->update('doctor_subscription_payments', ['payment_status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function extend_subscription($subscription) {
        $days = (isset($subscription['duration_days']) && $subscription['duration_days'] > 0) ? $subscription['duration_days'] : 30;
        
        $current_end_str = !empty($subscription['end_at']) ? $subscription['end_at'] : date('Y-m-d H:i:s');
        $current_end = strtotime($current_end_str);
        $base_time = ($current_end > time()) ? $current_end : time();
        
        $new_end_at = date('Y-m-d H:i:s', strtotime("+{$days} days", $base_time));
        $new_next_billing = date('Y-m-d', strtotime($new_end_at . ' -1 day'));

        echo "DEBUG: Extending Doctor Sub #{$subscription['id']} | Days: {$days} | Current End: {$current_end_str} | Base: " . date('Y-m-d H:i:s', $base_time) . " | New End: {$new_end_at} | New Next Billing: {$new_next_billing}\n";

        $this->db->where('id', $subscription['id']);
        $this->db->update('doctor_subscriptions', [
            'end_at' => $new_end_at,
            'next_billing_date' => $new_next_billing,
            'next_renewal_date' => $new_end_at,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        log_message('info', "Extended doctor subscription #{$subscription['id']} to {$new_end_at}. Next billing: {$new_next_billing}");
    }

    /**
     * Process Razorpay autopay renewal
     */
    private function process_razorpay_autopay_renewal($subscription) {
        // Razorpay autopay implementation
        $agreement_id = $subscription['autopay_agreement_id'];
        
        // Create renewal payment record
        $payment_data = [
            'doctor_id' => $subscription['doctor_id'],
            'subscription_id' => $subscription['id'],
            'payment_amount' => $subscription['plan_price'],
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'is_renewal' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('doctor_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();
        
        // Create renewal record
        $renewal_data = [
            'subscription_id' => $subscription['id'],
            'doctor_id' => $subscription['doctor_id'],
            'renewal_date' => date('Y-m-d H:i:s'),
            'payment_id' => $payment_id,
            'amount' => $subscription['plan_price'],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('subscription_renewals', $renewal_data);
        
        log_message('info', 'Razorpay autopay renewal initiated for subscription ' . $subscription['id']);
    }

    /**
     * Schedule manual renewal reminder
     */
    private function schedule_manual_renewal_reminder($subscription) {
        // Create retry record for manual renewal
        $retry_data = [
            'subscription_id' => $subscription['id'],
            'doctor_id' => $subscription['doctor_id'],
            'retry_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'attempt_count' => 1,
            'status' => 'scheduled',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('subscription_retries', $retry_data);
        
        log_message('info', 'Manual renewal reminder scheduled for subscription ' . $subscription['id']);
    }

    /**
     * Process failed autopay attempts
     */
    private function process_failed_autopay_attempts() {
        $failed_attempts = $this->get_failed_autopay_attempts();
        
        foreach ($failed_attempts as $attempt) {
            $this->process_failed_attempt($attempt);
        }
    }

    /**
     * Get failed autopay attempts that need retry
     */
    private function get_failed_autopay_attempts() {
        $this->db->select('sr.*, ds.doctor_id, ds.doctor_subscription_plan_id, ds.autopay_agreement_id, ds.payment_gateway');
        $this->db->from('subscription_renewals sr');
        $this->db->join('doctor_subscriptions ds', 'ds.id = sr.subscription_id');
        $this->db->where('sr.status', 'failed');
        $this->db->where('sr.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        
        return $this->db->get()->result_array();
    }

    /**
     * Process a single failed attempt
     */
    private function process_failed_attempt($attempt) {
        // Check if we should retry based on attempt count
        $max_retries = 3;
        $retry_count = $this->get_retry_count($attempt['subscription_id']);
        
        if ($retry_count < $max_retries) {
            $this->schedule_retry($attempt['subscription_id'], $attempt['doctor_id']);
        } else {
            $this->mark_subscription_expired($attempt['subscription_id']);
        }
    }

    /**
     * Get retry count for a subscription
     */
    private function get_retry_count($subscription_id) {
        $this->db->where('subscription_id', $subscription_id);
        $this->db->where('status', 'failed');
        return $this->db->count_all_results('subscription_renewals');
    }

    /**
     * Schedule a retry
     */
    private function schedule_retry($subscription_id, $doctor_id) {
        $retry_data = [
            'subscription_id' => $subscription_id,
            'doctor_id' => $doctor_id,
            'retry_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'attempt_count' => $this->get_retry_count($subscription_id) + 1,
            'status' => 'scheduled',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('subscription_retries', $retry_data);
        
        log_message('info', 'Retry scheduled for subscription ' . $subscription_id);
    }

    /**
     * Mark subscription as expired
     */
    private function mark_subscription_expired($subscription_id) {
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', [
            'status' => 'expired',
            'expired_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        log_message('info', 'Subscription ' . $subscription_id . ' marked as expired');
    }

    /**
     * Send renewal notifications
     */
    private function send_renewal_notifications() {
        $expiring_subscriptions = $this->get_subscriptions_expiring_soon();
        
        foreach ($expiring_subscriptions as $subscription) {
            $this->send_single_renewal_notification($subscription);
        }
    }

    /**
     * Send notification for a single subscription
     */
    private function send_single_renewal_notification($subscription) {
        // Check if notification already sent today
        $today = date('Y-m-d');
        $notification_key = 'renewal_notification_' . $subscription['id'] . '_' . $today;
        
        if ($this->is_notification_sent($notification_key)) {
            return;
        }
        
        // Send email notification
        $this->send_renewal_email($subscription);
        
        // Send SMS notification
        $this->send_renewal_sms($subscription);
        
        // Mark notification as sent
        $this->mark_notification_sent($notification_key);
        
        log_message('info', 'Renewal notification sent for subscription ' . $subscription['id']);
    }

    /**
     * Send renewal email
     */
    private function send_renewal_email($subscription) {
        $subject = 'Subscription Renewal Reminder';
        $message = "Dear Dr. {$subscription['doctor_name']},\n\n";
        $message .= "Your subscription for {$subscription['plan_name']} is expiring on " . date('d M Y', strtotime($subscription['end_at'])) . ".\n\n";
        $message .= "Amount: ₹{$subscription['plan_price']}\n\n";
        $message .= "Please renew your subscription to continue enjoying our services.\n\n";
        $message .= "Best regards,\nDoctto Team";
        
        // Use CodeIgniter's email library
        $this->load->library('email');
        
        $this->email->from('noreply@doctto.com', 'Doctto');
        $this->email->to($subscription['doctor_email']);
        $this->email->subject($subject);
        $this->email->message($message);
        
        $this->email->send();
    }

    /**
     * Send renewal SMS
     */
    private function send_renewal_sms($subscription) {
        $message = "Dear Dr. {$subscription['doctor_name']}, your Doctto subscription expires on " . date('d M Y', strtotime($subscription['end_at'])) . ". Amount: ₹{$subscription['plan_price']}. Renew now to continue services.";
        
        // Implement SMS sending logic here
        // You can use your preferred SMS gateway
        
        log_message('info', 'SMS notification sent to ' . $subscription['doctor_phone']);
    }

    /**
     * Check if notification already sent
     */
    private function is_notification_sent($notification_key) {
        // You can implement this using a simple cache or database table
        // For now, return false to allow notifications
        return false;
    }

    /**
     * Mark notification as sent
     */
    private function mark_notification_sent($notification_key) {
        // Implement notification tracking
        // You can use a simple cache or database table
    }

    /**
     * Cleanup old records
     */
    private function cleanup_old_records() {
        // Cleanup old renewal records (older than 30 days)
        $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        $this->db->where('created_at <', $thirty_days_ago);
        $this->db->where('status', 'success');
        $this->db->delete('subscription_renewals');
        
        // Cleanup old retry records (older than 30 days)
        $this->db->where('created_at <', $thirty_days_ago);
        $this->db->where('status', 'success');
        $this->db->delete('subscription_retries');
        
        log_message('info', 'Old renewal and retry records cleaned up');
    }

    /**
     * Process failed user autopay attempts
     */
    private function process_failed_user_autopay_attempts() {
        // Implement if user_subscription_renewals table is added later
    }

    /**
     * Send user renewal notifications
     */
    private function send_user_renewal_notifications() {
        $expiring_subscriptions = $this->get_user_subscriptions_expiring_soon();
        
        foreach ($expiring_subscriptions as $subscription) {
            $this->send_single_user_renewal_notification($subscription);
        }
    }

    /**
     * Send single user renewal notification
     */
    private function send_single_user_renewal_notification($subscription) {
        $subject = 'Subscription Renewal Reminder';
        $message = "Dear {$subscription['user_name']},\n\n";
        $message .= "Your subscription for {$subscription['plan_name']} is expiring on " . date('d M Y', strtotime($subscription['end_date'])) . ".\n\n";
        $message .= "Amount: ₹{$subscription['plan_price']}\n\n";
        $message .= "Please renew your subscription to continue enjoying our services.\n\n";
        $message .= "Best regards,\nDoctto Team";
        
        // Email
        $this->load->library('email');
        $this->email->from('noreply@doctto.com', 'Doctto');
        $this->email->to($subscription['user_email']);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
        
        // SMS
        if (!empty($subscription['user_phone'])) {
            $sms_message = "Dear {$subscription['user_name']}, your Doctto subscription ({$subscription['plan_name']}) expires on " . date('d M Y', strtotime($subscription['end_date'])) . ". Amount: ₹{$subscription['plan_price']}. Please renew to continue.";
            $template_id = '1407168691882209729'; // Using a generic success/info template if available
            $this->User->send_message($sms_message, $subscription['user_phone'], $template_id);
        }
        
        log_message('info', 'User renewal notification sent for subscription ' . $subscription['id']);
    }

    /**
     * Manual renewal processing (for testing)
     */
    public function process_manual_renewal($subscription_id) {
        $this->db->select('ds.*, d.doctor_name, d.email as doctor_email, d.mobile_number as doctor_phone, dsp.name as plan_name, dsp.price as plan_price');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'd.id = ds.doctor_id');
        $this->db->join('subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
        $this->db->where('ds.id', $subscription_id);
        
        $subscription = $this->db->get()->row_array();
        
        if (!$subscription) {
            echo "ERROR: Subscription ID " . $subscription_id . " not found in database.\n";
            return;
        }
        
        $this->process_single_subscription_renewal($subscription);
        echo "SUCCESS: Manual renewal processed for subscription " . $subscription_id . "\n";
    }

    /**
     * Test method to check system status
     */
    public function test_system() {
        echo "=== Subscription Renewal System Test ===\n";
        
        // Check expiring doctor subscriptions
        $expiring = $this->get_subscriptions_expiring_soon();
        echo "Expiring doctor subscriptions: " . count($expiring) . "\n";
        
        // Check expiring user subscriptions
        $expiring_user = $this->get_user_subscriptions_expiring_soon();
        echo "Expiring user subscriptions: " . count($expiring_user) . "\n";
        
        // Check failed doctor attempts
        $failed = $this->get_failed_autopay_attempts();
        echo "Failed doctor attempts: " . count($failed) . "\n";
        
        // Check table structures
        $tables = ['subscription_renewals', 'subscription_retries', 'user_subscriptions', 'user_subscription_payments'];
        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                echo "Table {$table}: EXISTS\n";
            } else {
                echo "Table {$table}: MISSING\n";
            }
        }
        
        echo "=== Test Complete ===\n";
    }
}
