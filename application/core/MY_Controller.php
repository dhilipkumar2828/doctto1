<?php

defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class MY_Controller extends CI_Controller {
    public $data;
     function __construct() {
        parent::__construct();
        $this->data = array();
        
        // Automatic AutoPay Trigger (No Cron solution)
        // Using direct DB access here to avoid model name collisions in children
        $this->execute_due_renewals_logic();
    }

    private function execute_due_renewals_logic() {
        $CI =& get_instance();
        $CI->load->database();
        
        // We look for subscriptions that are expiring in exactly 24 hours to NOTIFY them.
        $tomorrow_limit = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $due_subs = $CI->db->select('ds.*, dsp.price, dsp.duration_days')
            ->from('doctor_subscriptions ds')
            ->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id')
            ->where('ds.status', 'active')
            ->where('ds.autopay_enabled', 1)
            ->where('ds.end_at <=', $tomorrow_limit)
            ->get()->result();
        
        if (empty($due_subs)) return;
        
        $CI->load->library('PhonePeOAuthService');
        $token_result = $CI->phonepeoauthservice->getBearerToken();
        if (!$token_result['status']) return;

        foreach ($due_subs as $sub) {
            $cycle_id = 'NOTIFY_' . $sub->id . '_' . date('Ymd', strtotime($sub->end_at));
            
            // Check if already notified
            $exists = $CI->db->where('transaction_id', $cycle_id)->get('doctor_subscription_payments')->row();
            if ($exists) continue;

            $amount_in_paise = intval($sub->price * 100);
            $notify_result = $CI->phonepeoauthservice->notifyRedemption(
                $cycle_id,
                $sub->autopay_agreement_id,
                $amount_in_paise,
                $token_result['access_token'] ?? $token_result['accessToken'],
                true
            );
            
            if ($notify_result['status']) {
                $CI->db->insert('doctor_subscription_payments', array(
                    'doctor_id' => $sub->doctor_id,
                    'subscription_id' => $sub->id,
                    'payment_amount' => $sub->price,
                    'payment_method' => 'phonepe',
                    'payment_status' => 'notified',
                    'transaction_id' => $cycle_id,
                    'is_renewal' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ));
                log_message('info', "AutoPay Notified SUCCESS for Sub: " . $sub->id);
            } else {
                log_message('error', "AutoPay Notify FAIL for Sub " . $sub->id . " : " . json_encode($notify_result));
            }
        }
    }

    public function my_view($design_view) {
//        $this->load->view("includes/header", $this->data);
        $this->load->view($design_view);
//        $this->load->view("includes/footer");
    }
     function admin_view($design_view) {
       $this->load->view("admin/includes/header", $this->data);
//        $this->load->view("admin/menu", $this->data);
        $this->load->view("admin/" . $design_view);
      $this->load->view("admin/includes/footer");
    }

    
}