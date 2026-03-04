<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the PhonePe SDK via Composer Autoloader
require_once FCPATH . 'vendor/autoload.php';

/**
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Doctors_model $Doctors_model
 * @property CI_Email $email
 */
class Subscription_renewal_cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load required models
        $this->load->model('Doctors_model');
        $this->load->database();
        
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
            
            // Process failed autopay attempts
            $this->process_failed_autopay_attempts();
            
            // Send renewal notifications
            $this->send_renewal_notifications();
            
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
     * Get subscriptions expiring in the next 7 days
     */
    private function get_subscriptions_expiring_soon() {
        $seven_days_from_now = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        $this->db->select('ds.*, d.name as doctor_name, d.email as doctor_email, d.phone as doctor_phone, dsp.name as plan_name, dsp.price as plan_price');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'd.id = ds.doctor_id');
        $this->db->join('doctor_subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.auto_renew', 1);
        $this->db->where('ds.end_at <=', $seven_days_from_now);
        $this->db->where('ds.end_at >', date('Y-m-d H:i:s'));
        
        return $this->db->get()->result_array();
    }

    /**
     * Process renewal for a single subscription
     */
    private function process_single_subscription_renewal($subscription) {
        log_message('info', 'Processing renewal for subscription ID: ' . $subscription['id']);
        
        try {
            // Check if autopay is enabled
            if ($subscription['autopay_enabled'] && $subscription['autopay_agreement_id']) {
                $this->process_autopay_renewal($subscription);
            } else {
                // Schedule manual renewal reminder
                $this->schedule_manual_renewal_reminder($subscription);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Failed to process renewal for subscription ' . $subscription['id'] . ': ' . $e->getMessage());
            $this->schedule_retry($subscription['id'], $subscription['doctor_id']);
        }
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

    /**
     * Process PhonePe autopay renewal
     */
    private function process_phonepe_autopay_renewal($subscription) {
        $agreement_id = $subscription['autopay_agreement_id'];
        $merchant_order_id = 'REN' . time() . rand(100, 999);
        $amount_in_paise = intval($subscription['plan_price'] * 100);

        log_message('info', "Initiating PhonePe Recurring Debit for Sub: {$subscription['id']}, Order: {$merchant_order_id}");

        // Create renewal payment record first (pending)
        $payment_data = [
            'doctor_id' => $subscription['doctor_id'],
            'subscription_id' => $subscription['id'],
            'amount' => $subscription['plan_price'],
            'payment_gateway' => 'phonepe',
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
            $envString = (PHONEPE_MODE == 'PROD') ? \PhonePe\Env::PRODUCTION : \PhonePe\Env::UAT;

            // Manual token fetch for the backend call
            $token_url = (\PhonePe\Env::getBaseUrlForOAuth($envString)) . '/identity-manager/v1/oauth/token';
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
            
            // Recurring Debit API V2
            // Endpoint: /recurring/v2/debit
            $recurring_url = (\PhonePe\Env::getBaseUrl($envString)) . '/recurring/v2/debit';
            
            $debit_payload = json_encode([
                'merchantOrderId' => $merchant_order_id,
                'amount' => $amount_in_paise,
                'subscriptionId' => $agreement_id,
                'metaInfo' => [
                    'udf1' => (string)$subscription['doctor_id'],
                    'udf2' => 'RENEWAL',
                    'udf3' => (string)$subscription['id']
                ]
            ]);

            $ch = curl_init($recurring_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $debit_payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $res_data = json_decode($response);
            
            if ($http_code == 200 && isset($res_data->state) && ($res_data->state == 'COMPLETED' || $res_data->state == 'SUCCESS')) {
                // Success
                $this->db->where('id', $payment_id);
                $this->db->update('doctor_subscription_payments', ['payment_status' => 'success', 'phonepe_transaction_id' => $res_data->orderId ?? '']);
                
                // Update subscription expiry
                $this->extend_subscription($subscription);
                
                log_message('info', "PhonePe AutoPay Success for Sub: {$subscription['id']}");
            } else {
                // Failed or Pending
                $this->db->where('id', $payment_id);
                $this->db->update('doctor_subscription_payments', ['payment_status' => 'failed', 'error_message' => $response]);
                
                log_message('error', "PhonePe AutoPay Failed for Sub: {$subscription['id']}. Response: " . $response);
            }

        } catch (Exception $e) {
            log_message('error', "PhonePe AutoPay Exception for Sub: {$subscription['id']}: " . $e->getMessage());
            $this->db->where('id', $payment_id);
            $this->db->update('doctor_subscription_payments', ['payment_status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function extend_subscription($subscription) {
        $new_end_at = date('Y-m-d H:i:s', strtotime($subscription['end_at'] . " + {$subscription['duration_days']} days"));
        $this->db->where('id', $subscription['id']);
        $this->db->update('doctor_subscriptions', [
            'end_at' => $new_end_at,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
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
            'amount' => $subscription['plan_price'],
            'payment_gateway' => 'razorpay',
            'payment_status' => 'pending',
            'is_renewal' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('doctor_subscription_payments', $payment_data);
        $payment_id = $this->db->insert_id();
        
        // Create renewal record
        $renewal_data = [
            'subscriptionion_id' => $subscription['id'],
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
     * Manual renewal processing (for testing)
     */
    public function process_manual_renewal($subscription_id) {
        $this->db->select('ds.*, d.name as doctor_name, d.email as doctor_email, d.phone as doctor_phone, dsp.name as plan_name, dsp.price as plan_price');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'd.id = ds.doctor_id');
        $this->db->join('doctor_subscription_plans dsp', 'dsp.id = ds.doctor_subscription_plan_id');
        $this->db->where('ds.id', $subscription_id);
        
        $subscription = $this->db->get()->row_array();
        
        if (!$subscription) {
            echo "ERROR: Subscription not found\n";
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
        
        // Check expiring subscriptions
        $expiring = $this->get_subscriptions_expiring_soon();
        echo "Expiring subscriptions: " . count($expiring) . "\n";
        
        // Check failed attempts
        $failed = $this->get_failed_autopay_attempts();
        echo "Failed attempts: " . count($failed) . "\n";
        
        // Check table structures
        $tables = ['subscription_renewals', 'subscription_retries'];
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
