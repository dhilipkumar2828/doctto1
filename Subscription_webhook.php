<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Subscription_webhook extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('doctors_model');
        $this->load->database();
    }

    // PhonePe webhook for subscription payments using SDK verification
    function phonepe_subscription_webhook() {
        // Load PhonePe SDK Client
        $this->load->library('PhonePeSDKClient');
        
        $payload = file_get_contents('php://input');
        $json_decode = json_decode($payload);
        
        if (!$json_decode || !isset($json_decode->response)) {
            log_message('error', 'PhonePe Subscription Webhook: Invalid payload received');
            http_response_code(400);
            return;
        }

        // Get authorization header for verification
        $authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        $username = PHONEPE_CLIENT_ID; // Use client ID as username
        $password = PHONEPE_CLIENT_SECRET; // Use client secret as password
        
        // Verify webhook using PhonePe SDK
        $verification_result = $this->phonepesdkclient->verifyCallbackResponse(
            $username,
            $password,
            $authorization,
            $payload
        );

        if (!$verification_result['status']) {
            log_message('error', 'PhonePe Subscription Webhook: Verification failed - ' . $verification_result['message']);
            http_response_code(401);
            return;
        }

        $callback_data = $verification_result['data'];
        
        // Store webhook response for debugging
        $webhook_data = array(
            'pay_transaction_id' => $callback_data['merchantOrderId'],
            'json_file' => $payload,
            'webhook_type' => 'subscription_sdk_verified',
            'created_at' => time()
        );
        
        $this->db->insert('webhook_response', $webhook_data);

        // Process subscription payment with SDK-verified data
        $transaction_id = $callback_data['merchantOrderId'];
        $payment_status = $callback_data['status'];
        $amount = isset($callback_data['amount']) ? $callback_data['amount'] / 100 : 0;
        $error_code = isset($callback_data['errorCode']) ? $callback_data['errorCode'] : null;
        $error_message = isset($callback_data['errorMessage']) ? $callback_data['errorMessage'] : null;

        // Find subscription payment record
        $this->db->where('transaction_id', $transaction_id);
        $payment = $this->db->get('doctor_subscription_payments')->row();

        if (!$payment) {
            log_message('error', 'PhonePe Subscription Webhook: Payment record not found for transaction: ' . $transaction_id);
            http_response_code(404);
            return;
        }

        // Enhanced payment status mapping
        $final_status = $this->mapPhonePeStatus($payment_status, $error_code);
        
        // Update payment status with detailed information
        $this->db->where('id', $payment->id);
        $this->db->update('doctor_subscription_payments', array(
            'payment_status' => $final_status,
            'error_code' => $error_code,
            'error_message' => $error_message,
            'phonepe_response' => json_encode($callback_data),
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Process payment based on comprehensive status
        $this->processPaymentStatus($payment, $final_status, $error_code, $error_message);
        
        // Return HTTP 200 for successful processing
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Webhook processed successfully']);
    }

    // Razorpay webhook for subscription payments
    function razorpay_subscription_webhook() {
        $payload = file_get_contents('php://input');
        $json_decode = json_decode($payload);
        
        if (!$json_decode) {
            log_message('error', 'Razorpay Subscription Webhook: Invalid payload received');
            return;
        }

        // Store webhook response for debugging
        $webhook_data = array(
            'pay_transaction_id' => isset($json_decode->payload->payment->entity->id) ? $json_decode->payload->payment->entity->id : 'unknown',
            'json_file' => $payload,
            'webhook_type' => 'razorpay_subscription',
            'created_at' => time()
        );
        
        $this->db->insert('webhook_response', $webhook_data);

        // Process Razorpay payment with comprehensive handling
        if (isset($json_decode->payload->payment->entity)) {
            $payment_entity = $json_decode->payload->payment->entity;
            $payment_id = $payment_entity->id;
            $order_id = $payment_entity->order_id;
            $payment_status = $payment_entity->status;
            $amount = $payment_entity->amount / 100;
            $error_code = isset($payment_entity->error_code) ? $payment_entity->error_code : null;
            $error_description = isset($payment_entity->error_description) ? $payment_entity->error_description : null;

            // Find subscription payment record
            $this->db->where('razorpay_order_id', $order_id);
            $payment = $this->db->get('doctor_subscription_payments')->row();

            if (!$payment) {
                log_message('error', 'Razorpay Subscription Webhook: Payment record not found for order: ' . $order_id);
                return;
            }

            // Enhanced payment status mapping
            $final_status = $this->mapRazorpayStatus($payment_status, $error_code);
            
            // Update payment status with detailed information
            $this->db->where('id', $payment->id);
            $this->db->update('doctor_subscription_payments', array(
                'payment_status' => $final_status,
                'razorpay_payment_id' => $payment_id,
                'error_code' => $error_code,
                'error_message' => $error_description,
                'updated_at' => date('Y-m-d H:i:s')
            ));

            // Process payment based on comprehensive status
            $this->processPaymentStatus($payment, $final_status, $error_code, $error_description);
        }
    }

    // Comprehensive payment status processing
    private function processPaymentStatus($payment, $status, $error_code = null, $error_message = null) {
        $subscription_id = $payment->subscription_id;
        
        switch ($status) {
            case 'PAYMENT_SUCCESS':
            case 'captured':
                $this->activateSubscription($subscription_id, $payment->doctor_id);
                $this->setupAutopayRenewal($subscription_id, $payment->doctor_id);
                $this->sendSubscriptionActivationNotification($payment->doctor_id, $subscription_id);
                log_message('info', "Subscription activated successfully for ID: $subscription_id");
                break;
                
            case 'PAYMENT_FAILED':
            case 'failed':
                $this->markSubscriptionFailed($subscription_id, $error_code, $error_message);
                $this->sendPaymentFailureNotification($payment->doctor_id, $subscription_id, $error_message);
                log_message('error', "Payment failed for subscription ID: $subscription_id, Error: $error_message");
                break;
                
            case 'PAYMENT_PENDING':
            case 'pending':
                $this->markSubscriptionPending($subscription_id);
                $this->sendPaymentPendingNotification($payment->doctor_id, $subscription_id);
                log_message('info', "Payment pending for subscription ID: $subscription_id");
                break;
                
            case 'PAYMENT_DECLINED':
            case 'declined':
                $this->markSubscriptionDeclined($subscription_id, $error_code, $error_message);
                $this->sendPaymentDeclinedNotification($payment->doctor_id, $subscription_id, $error_message);
                log_message('error', "Payment declined for subscription ID: $subscription_id, Error: $error_message");
                break;
                
            default:
                log_message('warning', "Unknown payment status: $status for subscription ID: $subscription_id");
                break;
        }
    }

    // PhonePe status mapping
    private function mapPhonePeStatus($status, $error_code = null) {
        if ($status == 'PAYMENT_SUCCESS') return 'PAYMENT_SUCCESS';
        if ($status == 'PAYMENT_FAILED') return 'PAYMENT_FAILED';
        if ($status == 'PAYMENT_PENDING') return 'PAYMENT_PENDING';
        if ($error_code) return 'PAYMENT_FAILED';
        return 'PAYMENT_PENDING';
    }

    // Razorpay status mapping
    private function mapRazorpayStatus($status, $error_code = null) {
        if ($status == 'captured') return 'PAYMENT_SUCCESS';
        if ($status == 'failed') return 'PAYMENT_FAILED';
        if ($status == 'pending') return 'PAYMENT_PENDING';
        if ($status == 'cancelled') return 'PAYMENT_DECLINED';
        if ($error_code) return 'PAYMENT_FAILED';
        return 'PAYMENT_PENDING';
    }

    // Activate subscription
    private function activateSubscription($subscription_id, $doctor_id) {
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'active',
            'activated_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Update doctor's subscription status
        $this->db->where('id', $doctor_id);
        $this->db->update('doctors', array(
            'has_active_subscription' => 1,
            'subscription_updated_at' => date('Y-m-d H:i:s')
        ));
    }

    // Setup autopay renewal
    private function setupAutopayRenewal($subscription_id, $doctor_id) {
        // Get subscription details
        $this->db->where('id', $subscription_id);
        $subscription = $this->db->get('doctor_subscriptions')->row();
        
        if ($subscription && $subscription->auto_renewal) {
            // Schedule next renewal
            $next_renewal_date = date('Y-m-d H:i:s', strtotime($subscription->end_at . ' -1 day'));
            
            $renewal_data = array(
                'subscription_id' => $subscription_id,
                'doctor_id' => $doctor_id,
                'renewal_date' => $next_renewal_date,
                'status' => 'scheduled',
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('subscription_renewals', $renewal_data);
            log_message('info', "Autopay renewal scheduled for subscription ID: $subscription_id");
        }
    }

    // Mark subscription as failed
    private function markSubscriptionFailed($subscription_id, $error_code = null, $error_message = null) {
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'failed',
            'error_code' => $error_code,
            'error_message' => $error_message,
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    // Mark subscription as pending
    private function markSubscriptionPending($subscription_id) {
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    // Mark subscription as declined
    private function markSubscriptionDeclined($subscription_id, $error_code = null, $error_message = null) {
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'declined',
            'error_code' => $error_code,
            'error_message' => $error_message,
            'updated_at' => date('Y-m-d H:i:s')
        ));
    }

    // Send subscription activation notification
    private function sendSubscriptionActivationNotification($doctor_id, $subscription_id) {
        $this->db->select('ds.*, dsp.name as plan_name, d.doctor_name');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        $this->db->join('doctors d', 'ds.doctor_id = d.id');
        $this->db->where('ds.id', $subscription_id);
        $subscription = $this->db->get()->row();

        if ($subscription) {
            $notification_data = array(
                'appointment_id' => 0,
                'sender_id' => 0,
                'recieved_id' => $doctor_id,
                'message' => "Your {$subscription->plan_name} subscription has been activated successfully! Valid until " . date('d M Y', strtotime($subscription->end_at)),
                'created_date' => date('Y-m-d'),
                'created_at' => time(),
                'title' => 'Subscription Activated'
            );
            
            $this->db->insert('doctor_notifications', $notification_data);
        }
    }

    // Send payment failure notification
    private function sendPaymentFailureNotification($doctor_id, $subscription_id, $error_message = null) {
        $message = "Your subscription payment has failed.";
        if ($error_message) {
            $message .= " Error: $error_message";
        }
        
        $notification_data = array(
            'appointment_id' => 0,
            'sender_id' => 0,
            'recieved_id' => $doctor_id,
            'message' => $message,
            'created_date' => date('Y-m-d'),
            'created_at' => time(),
            'title' => 'Payment Failed'
        );
        
        $this->db->insert('doctor_notifications', $notification_data);
    }

    // Send payment pending notification
    private function sendPaymentPendingNotification($doctor_id, $subscription_id) {
        $notification_data = array(
            'appointment_id' => 0,
            'sender_id' => 0,
            'recieved_id' => $doctor_id,
            'message' => "Your subscription payment is pending. Please complete the payment to activate your subscription.",
            'created_date' => date('Y-m-d'),
            'created_at' => time(),
            'title' => 'Payment Pending'
        );
        
        $this->db->insert('doctor_notifications', $notification_data);
    }

    // Send payment declined notification
    private function sendPaymentDeclinedNotification($doctor_id, $subscription_id, $error_message = null) {
        $message = "Your subscription payment was declined.";
        if ($error_message) {
            $message .= " Reason: $error_message";
        }
        
        $notification_data = array(
            'appointment_id' => 0,
            'sender_id' => 0,
            'recieved_id' => $doctor_id,
            'message' => $message,
            'created_date' => date('Y-m-d'),
            'created_at' => time(),
            'title' => 'Payment Declined'
        );
        
        $this->db->insert('doctor_notifications', $notification_data);
    }

    // Subscription cancellation endpoint
    function cancel_subscription() {
        $subscription_id = $this->input->post('subscription_id');
        $doctor_id = $this->input->post('doctor_id');
        $reason = $this->input->post('reason', 'No reason provided');
        
        if (!$subscription_id || !$doctor_id) {
            echo json_encode(['status' => false, 'message' => 'Missing required parameters']);
            return;
        }

        // Verify ownership
        $this->db->where('id', $subscription_id);
        $this->db->where('doctor_id', $doctor_id);
        $subscription = $this->db->get('doctor_subscriptions')->row();

        if (!$subscription) {
            echo json_encode(['status' => false, 'message' => 'Subscription not found or access denied']);
            return;
        }

        // Cancel subscription
        $this->db->where('id', $subscription_id);
        $this->db->update('doctor_subscriptions', array(
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancellation_reason' => $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Cancel any pending renewals
        $this->db->where('subscription_id', $subscription_id);
        $this->db->where('status', 'scheduled');
        $this->db->update('subscription_renewals', array(
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ));

        // Update doctor's subscription status
        $this->db->where('id', $doctor_id);
        $this->db->update('doctors', array(
            'has_active_subscription' => 0,
            'subscription_updated_at' => date('Y-m-d H:i:s')
        ));

        // Send cancellation notification
        $notification_data = array(
            'appointment_id' => 0,
            'sender_id' => 0,
            'recieved_id' => $doctor_id,
            'message' => "Your subscription has been cancelled. Reason: $reason",
            'created_date' => date('Y-m-d'),
            'created_at' => time(),
            'title' => 'Subscription Cancelled'
        );
        
        $this->db->insert('doctor_notifications', $notification_data);

        echo json_encode(['status' => true, 'message' => 'Subscription cancelled successfully']);
    }

    // Production webhook endpoint - no test functions
}
