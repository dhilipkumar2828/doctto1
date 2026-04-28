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
class Subscription_api extends REST_Controller {

    public function __construct() {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers,Authorization,Access-Control-Allow-Origin,Access-Control-Allow-Methods");
        parent::__construct();
        $this->load->model('subscription_api_model');
        $this->load->model('common_model');
    }

    /**
     * Get all available subscription plans
     * POST Param: type (doctor/customer)
     */
    public function plans_list_post() {
        $type = $this->post('type');
        if (!$type) {
            $type = 'customer';
        }

        $plans = $this->subscription_api_model->get_plans($type);
        if ($plans) {
            $this->response([
                'status' => 'success',
                'data' => $plans
              ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'No plans found'
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get customer subscription plans
     * GET/POST
     */
    public function customer_plans_list_post() {
        // Authenticate request
        $auth = null;
        if (method_exists($this->common_model, 'auth')) {
            $auth = $this->common_model->auth();
        }

        // STRICT CHECK: Only allow 'user' (customer)
        if ($auth && $auth->type !== 'user' && $auth->type !== 'jwt') {
            $this->response([
                'status' => 'error', 
                'message' => 'Unauthorized: This endpoint is for customers only.'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        $plans = $this->subscription_api_model->get_plans('customer');
        
        // Check current active subscription to mark 'is_subscribed'
        $active_sub = null;
        if ($auth && $auth->id) {
            $active_sub = $this->subscription_api_model->get_my_subscription($auth->id, 'customer');
        }

        if ($plans) {
            foreach ($plans as &$p) {
                $p->is_subscribed = ($active_sub && $active_sub->plan_id == $p->id) ? 1 : 0;
            }
            $this->response(['status' => 'success', 'data' => $plans], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => "No customer plans found"], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get doctor subscription plans
     * GET/POST
     */
    public function doctor_plans_list_post() {
        // Authenticate request
        $auth = null;
        if (method_exists($this->common_model, 'auth')) {
            $auth = $this->common_model->auth();
        }

        // STRICT CHECK: Only allow 'doctor'
        if ($auth && $auth->type !== 'doctor' && $auth->type !== 'jwt') {
            $this->response([
                'status' => 'error', 
                'message' => 'Unauthorized: This endpoint is for doctors only.'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $plans = $this->subscription_api_model->get_plans('doctor');

        // Check current active subscription to mark 'is_subscribed'
        $doctor_id = ($auth && $auth->id) ? $auth->id : $this->input->get_post('doctor_id');
        if (!$doctor_id) {
            $stream_data = json_decode($this->input->raw_input_stream, true);
            $doctor_id = $stream_data['doctor_id'] ?? null;
        }

        $active_sub = null;
        if ($doctor_id) {
            $active_sub = $this->subscription_api_model->get_my_subscription($doctor_id, 'doctor');
        }

        if ($plans) {
            foreach ($plans as &$p) {
                $p->is_subscribed = ($active_sub && $active_sub->doctor_subscription_plan_id == $p->id) ? 1 : 0;
            }
            $this->response(['status' => 'success', 'data' => $plans], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => "No doctor plans found"], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get the popular subscription plan
     * POST Param: type (doctor/customer)
     */
    public function popular_plan_post() {
        $type = $this->post('type') ? $this->post('type') : $this->post('trpe');
        if (!$type) {
            $type = 'doctor';
        }

        $name = $this->post('name') ? $this->post('name') : 'Popular Plan';

        $plan = $this->subscription_api_model->get_plan_by_name($name, $type);
        if ($plan) {
            $this->response([
                'status' => 'success',
                'data' => $plan
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'Plan not found'
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get active subscription for a specific user/doctor
     * POST Params: id, type (doctor/customer)
     */
    public function my_subscription_post() {
        // Authenticate request
        $auth = null;
        if (method_exists($this->common_model, 'auth')) {
            $auth = $this->common_model->auth();
        }

        $id = $this->post('id') ? $this->post('id') : ($this->post('user_id') ? $this->post('user_id') : $this->post('doctor_id'));
        $type = $this->post('type') ? $this->post('type') : $this->post('plan_type');

        if (!$id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing ID or Type'], REST_Controller::HTTP_OK);
            return;
        }

        // SECURITY CHECK: Ensure user can only see their own subscription
        if ($auth && $auth->id && $auth->id != $id) {
            $this->response(['status' => 'error', 'message' => 'Unauthorized: You can only view your own subscription.'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        // SECURITY CHECK: Ensure type matches
        $check_type = ($type == 'customer') ? 'user' : $type;
        if ($auth && $auth->type != 'jwt' && $auth->type != $check_type) {
            $this->response(['status' => 'error', 'message' => 'Unauthorized: Account type mismatch.'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $subscription = $this->subscription_api_model->get_my_subscription($id, $type);
        $history = $this->subscription_api_model->get_history($id, $type);
        
        if ($subscription) {
            // Check expiry
            $end_date = ($type == 'doctor') ? $subscription->end_at : $subscription->end_date;
            $remaining_seconds = strtotime($end_date) - time();
            $remaining_days = ceil($remaining_seconds / (60 * 60 * 24));

            if ($remaining_seconds <= 0) {
                // Automatically mark as expired if found active but date passed
                $this->response([
                    'status' => 'expired',
                    'message' => 'Your subscription has expired. Please renew or purchase a new plan.',
                    'expired_at' => $end_date,
                    'data' => $subscription,
                    'history' => $history
                ], REST_Controller::HTTP_OK);
            } else {
                $start_date = ($type == 'doctor') ? ($subscription->start_at ?? '') : ($subscription->start_date ?? '');
                $formatted_start = !empty($start_date) ? date('d M Y', strtotime($start_date)) : '';
                $formatted_end = !empty($end_date) ? date('d M Y', strtotime($end_date)) : '';
                
                $subscription->remaining_days = $formatted_end;
                
                // If customer, fetch subscribed doctors
                if ($type == 'customer') {
                    $plan = $this->subscription_api_model->get_plan_details($subscription->plan_id);
                    $subscription->max_doctors_allowed = (int)($plan->max_doctors_allowed ?? 0);
                    $subscription->subscribed_doctors = $this->subscription_api_model->get_user_subscribed_doctors($subscription->id);
                    $subscription->doctors_count = count($subscription->subscribed_doctors);
                    $subscription->doctors_remaining = max(0, $subscription->max_doctors_allowed - $subscription->doctors_count);
                }

                $this->response([
                    'status' => 'success',
                    'message' => 'Subscription is active',
                    'remaining_days' => $subscription->remaining_days,
                    'data' => $subscription,
                    'history' => $history
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'No active subscription found. You can purchase a new plan.',
                'history' => $history
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Customer Select/Buy Plan
     * POST Params: user_id, plan_id, payment_id (optional)
     */
    public function customer_buy_plan_post() {
        $user_id = $this->post('user_id');
        $plan_id = $this->post('plan_id');
        $payment_id = $this->post('payment_id');

        if (!$user_id || !$plan_id) {
            $this->response(['status' => 'error', 'message' => 'Missing User ID or Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $data = [
            'type' => 'customer',
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'duration' => $plan->duration_days,
            'amount' => $plan->price,
            'payment_id' => $payment_id,
            'payment_status' => $this->post('payment_status') ?? 'completed',
            'payment_gateway' => $this->post('payment_gateway') ?? 'phonepe',
            'auto_renew' => $this->post('auto_renew') ?? 1,
            'autopay_enabled' => $this->post('autopay_enabled') ?? 0,
            'autopay_agreement_id' => $this->post('autopay_agreement_id'),
            'merchant_subscription_id' => $this->post('merchant_subscription_id'),
            'phonepe_subscription_id' => $this->post('phonepe_subscription_id'),
            'next_billing_date' => $this->post('next_billing_date')
        ];

        $result = $this->subscription_api_model->buy_subscription($data);
        if ($result === 'already_active') {
             $this->response(['status' => 'error', 'message' => 'You already have an active subscription for this plan.'], REST_Controller::HTTP_BAD_REQUEST);
        } else if ($result) {
            $this->response(['status' => 'success', 'message' => 'Plan selected successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Failed to process subscription'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Doctor Select/Buy Plan
     * POST Params: doctor_id, plan_id, payment_id (optional)
     */
    public function doctor_buy_plan_post() {
        $doctor_id = $this->post('doctor_id');
        $plan_id = $this->post('plan_id');
        $payment_id = $this->post('payment_id');

        if (!$doctor_id || !$plan_id) {
            $this->response(['status' => 'error', 'message' => 'Missing Doctor ID or Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if (!$plan) {
            $this->response(['status' => 'error', 'message' => 'Invalid Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $data = [
            'type' => 'doctor',
            'doctor_id' => $doctor_id,
            'plan_id' => $plan_id,
            'duration' => $plan->duration_days,
            'amount' => $plan->price,
            'payment_id' => $payment_id,
            'payment_status' => $this->post('payment_status') ?? 'completed',
            'payment_gateway' => $this->post('payment_gateway') ?? 'phonepe',
            'auto_renew' => $this->post('auto_renew') ?? 1,
            'autopay_enabled' => $this->post('autopay_enabled') ?? 0,
            'autopay_agreement_id' => $this->post('autopay_agreement_id'),
            'merchant_subscription_id' => $this->post('merchant_subscription_id'),
            'phonepe_subscription_id' => $this->post('phonepe_subscription_id'),
            'next_billing_date' => $this->post('next_billing_date')
        ];

        $result = $this->subscription_api_model->buy_subscription($data);
        if ($result === 'already_active') {
             $this->response(['status' => 'error', 'message' => 'You already have an active subscription for this plan.'], REST_Controller::HTTP_BAD_REQUEST);
        } else if ($result) {
            $this->response(['status' => 'success', 'message' => 'Plan selected successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Failed to process subscription'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Record a new subscription purchase (Unified API)
     * POST Params: id, type, plan_id, duration, amount, payment_id
     */
    public function buy_subscription_post() {
        $type = $this->post('type') ? $this->post('type') : $this->post('plan_type');
        $plan_id = $this->post('plan_id');
        $id = $this->post('id'); // user_id or doctor_id
        $duration = $this->post('duration') ? $this->post('duration') : $this->post('duration_days');
        $amount = $this->post('amount') ? $this->post('amount') : $this->post('price');
        $payment_id = $this->post('payment_id');

        if (!$id || !$type || !$plan_id || !$duration || !$amount) {
            $this->response([
                'status' => 'error',
                'message' => 'Missing required fields'
            ], REST_Controller::HTTP_OK);
            return;
        }

        $data = [
            'type' => $type,
            'plan_id' => $plan_id,
            'duration' => $duration,
            'amount' => $amount,
            'payment_id' => $payment_id,
            'payment_status' => $this->post('payment_status') ?? 'completed',
            'payment_gateway' => $this->post('payment_gateway') ?? $this->post('gateway') ?? 'phonepe',
            'auto_renew' => $this->post('auto_renew') ?? 1,
            'autopay_enabled' => $this->post('autopay_enabled') ?? 0,
            'autopay_agreement_id' => $this->post('autopay_agreement_id') ?? $this->post('agreement_id'),
            'merchant_subscription_id' => $this->post('merchant_subscription_id') ?? $this->post('merchant_sub_id'),
            'phonepe_subscription_id' => $this->post('phonepe_subscription_id') ?? $this->post('sub_id'),
            'next_billing_date' => $this->post('next_billing_date')
        ];

        if ($type == 'doctor') {
            $data['doctor_id'] = $id;
        } else {
            $data['user_id'] = $id;
        }

        $result = $this->subscription_api_model->buy_subscription($data);
        if ($result === 'already_active') {
            $this->response(['status' => 'error', 'message' => 'You already have an active subscription for this plan.'], REST_Controller::HTTP_BAD_REQUEST);
        } elseif ($result) {
            $this->response([
                'status' => 'success',
                'message' => 'Subscription activated successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'Failed to activate subscription'
            ], REST_Controller::HTTP_OK);
        }
    }
    /**
     * Get subscription history for a specific user/doctor
     * POST Params: id, type (doctor/customer)
     */
    public function subscription_history_post() {
        // Authenticate request
        if (method_exists($this->common_model, 'auth')) {
            $this->common_model->auth();
        }

        $id = $this->post('id');
        $type = $this->post('type') ? $this->post('type') : $this->post('plan_type');

        if (!$id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing ID or Type'], REST_Controller::HTTP_OK);
            return;
        }

        $history = $this->subscription_api_model->get_history($id, $type);
        $this->response(['status' => 'success', 'data' => $history], REST_Controller::HTTP_OK);
    }

    /**
     * Cancel an active subscription
     * POST Params: id, type (doctor/customer)
     */
    public function cancel_subscription_post() {
        $id = $this->post('id');
        $type = $this->post('type') ? $this->post('type') : $this->post('plan_type');

        if (!$id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing ID or Type'], REST_Controller::HTTP_OK);
            return;
        }

        $result = $this->subscription_api_model->cancel_subscription($id, $type);
        if ($result) {
            $this->response(['status' => 'success', 'message' => 'Subscription cancelled successfully'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Failed to cancel or no active subscription found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get details for a specific plan
     * POST Param: plan_id
     */
    public function plan_details_post() {
        $plan_id = $this->post('plan_id');

        if (!$plan_id) {
            $this->response(['status' => 'error', 'message' => 'Missing Plan ID'], REST_Controller::HTTP_OK);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($plan_id);
        if ($plan) {
            $this->response(['status' => 'success', 'data' => $plan], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'Plan not found'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get details for a specific doctor plan
     * GET /api/subscription-plans/doctor/:id
     */
    public function plan_get($id = NULL) {
        // Authenticate request
        $auth = null;
        if (method_exists($this->common_model, 'auth')) {
            $auth = $this->common_model->auth();
        }

        if (!$id) {
            $this->response([
                'success' => false,
                'message' => 'Plan ID is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $plan = $this->subscription_api_model->get_plan_details($id);
        
        if ($plan && $plan->plan_type == 'doctor') {
            // Check current doctor's status for THIS specific plan using Model
            $my_status = 'not_subscribed';
            if ($auth && $auth->id) {
                $my_status = $this->subscription_api_model->get_subscription_status($auth->id, $id, $auth->type == 'doctor' ? 'doctor' : 'customer');
                
                // Map 'expired' to 'inactive' for better user clarity
                if ($my_status == 'expired') {
                    $my_status = 'inactive';
                }
            }

            // Format features
            $features = [];
            if (!empty($plan->description)) {
                $features = array_map('trim', explode(',', $plan->description));
            } elseif (!empty($plan->perks)) {
                $features = array_map('trim', explode(',', $plan->perks));
            }

            // Fallback for features if empty
            if (empty($features)) {
                $features = ["Chat, Audio & Video Consultations", "24/7 Access"];
            }

            $this->response([
                'success' => true,
                'my_subscription_status' => $my_status,
                'data' => [
                    'id' => (int)$plan->id,
                    'type' => $plan->plan_type,
                    'name' => $plan->name,
                    'price' => (float)$plan->price,
                    'billing_cycle' => $plan->duration_days . ' days',
                    'doctors_per_month' => (int)($plan->max_doctors_allowed ?? 1),
                    'features' => $features,
                    'call_chat_number' => $plan->call_chat ?? "+91XXXXXXXXXX",
                    'whatsapp_chat_number' => $plan->whatsapp_chat ?? "+91XXXXXXXXXX",
                    'fair_usage_policy' => $plan->fair_usage_policy ?? "3 consultations / month",
                    'is_active' => $plan->is_active == 1,
                    'created_at' => date('c', strtotime($plan->created_at)),
                    'updated_at' => date('c', strtotime($plan->updated_at ?? $plan->created_at))
                ]
            ], REST_Controller::HTTP_OK);
        } else {
        $this->response([
            'success' => false,
            'message' => 'Doctor Plan not found'
        ], REST_Controller::HTTP_NOT_FOUND);
    }
}

/**
 * Get details for a specific customer plan
 * GET /api/subscription-plans/customer/:id
 */
public function customer_plan_get($id = NULL) {
    // Authenticate request
    $auth = null;
    if (method_exists($this->common_model, 'auth')) {
        $auth = $this->common_model->auth();
    }

    if (!$id) {
        $this->response([
            'success' => false,
            'message' => 'Plan ID is required'
        ], REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    $plan = $this->subscription_api_model->get_plan_details($id);
    
    if ($plan && $plan->plan_type == 'customer') {
        // Check current user's status for THIS specific plan using Model
        $my_status = 'not_subscribed';
        if ($auth && $auth->id) {
            $my_status = $this->subscription_api_model->get_subscription_status($auth->id, $id, 'customer');
            
            // Map 'expired' to 'inactive' for better user clarity
            if ($my_status == 'expired') {
                $my_status = 'inactive';
            }
        }

        // Format features
        $features = [];
        if (!empty($plan->description)) {
            $features = array_map('trim', explode(',', $plan->description));
        } elseif (!empty($plan->perks)) {
            $features = array_map('trim', explode(',', $plan->perks));
        }

        if (empty($features)) {
            $features = ["Consult with specialized doctors", "24/7 Support"];
        }

        $this->response([
            'success' => true,
            'my_subscription_status' => $my_status,
            'data' => [
                'id' => (int)$plan->id,
                'type' => $plan->plan_type,
                'name' => $plan->name,
                'price' => (float)$plan->price,
                'billing_cycle' => $plan->duration_days . ' days',
                'max_doctors_allowed' => (int)($plan->max_doctors_allowed ?? 1),
                'total_consultations' => (int)($plan->total_consultations ?? 0),
                'features' => $features,
                'is_active' => $plan->is_active == 1,
                'created_at' => date('c', strtotime($plan->created_at)),
                'updated_at' => date('c', strtotime($plan->updated_at ?? $plan->created_at))
            ]
        ], REST_Controller::HTTP_OK);
    } else {
        $this->response([
            'success' => false,
            'message' => 'Customer Plan not found'
        ], REST_Controller::HTTP_NOT_FOUND);
    }
}

/**
 * Customer subscribe to a doctor
 * POST Params: user_id, doctor_id
 */
public function subscribe_to_doctor_post() {
    $user_id = $this->post('user_id');
    $doctor_id = $this->post('doctor_id');

    if (!$user_id || !$doctor_id) {
        $this->response(['status' => 'error', 'message' => 'Missing User ID or Doctor ID'], REST_Controller::HTTP_OK);
        return;
    }

    // 1. Get user's active subscription
    $subscription = $this->subscription_api_model->get_my_subscription($user_id, 'customer');

    if (!$subscription) {
        $this->response(['status' => 'error', 'message' => 'No active subscription found. Please buy a plan first.'], REST_Controller::HTTP_OK);
        return;
    }

    // 2. Double check the plan limit
    $plan = $this->subscription_api_model->get_plan_details($subscription->plan_id);
    if (!$plan) {
         $this->response(['status' => 'error', 'message' => 'Subscription plan details not found.'], REST_Controller::HTTP_OK);
         return;
    }

    $result = $this->subscription_api_model->subscribe_to_doctor($user_id, $subscription->id, $doctor_id, $plan->max_doctors_allowed);

    if ($result === 'already_subscribed') {
        $this->response(['status' => 'error', 'message' => 'You are already subscribed to this doctor.'], REST_Controller::HTTP_OK);
    } elseif ($result === 'limit_reached') {
        $this->response(['status' => 'error', 'message' => 'Subscription limit reached. You cannot subscribe to more doctors.'], REST_Controller::HTTP_OK);
    } elseif ($result) {
        $this->response(['status' => 'success', 'message' => 'Successfully subscribed to doctor'], REST_Controller::HTTP_OK);
    } else {
        $this->response(['status' => 'error', 'message' => 'Failed to subscribe to doctor'], REST_Controller::HTTP_OK);
    }
}

    /**
     * Create a new subscription plan
     * POST /api/subscription-plans/doctor
     */
    public function plan_post() {
        // Authenticate request
        $auth = null;
        if (method_exists($this->common_model, 'auth')) {
            $auth = $this->common_model->auth();
        }

        // Get inputs from POST or JSON stream
        $doctor_id = $this->post('doctor_id');
        $plan_id = $this->post('plan_id');
        $type = $this->post('type') ?? 'doctor';
        $name = $this->post('name');
        $price = $this->post('price');
        $billing_cycle = $this->post('billing_cycle');

        if (empty($doctor_id) || empty($plan_id)) {
            $stream_data = json_decode($this->input->raw_input_stream, true);
            if (!empty($stream_data)) {
                $doctor_id = $stream_data['doctor_id'] ?? $doctor_id;
                $plan_id = $stream_data['plan_id'] ?? $plan_id;
                $type = $stream_data['type'] ?? $type;
                $name = $stream_data['name'] ?? $name;
                $price = $stream_data['price'] ?? $price;
                $billing_cycle = $stream_data['billing_cycle'] ?? $billing_cycle;
            }
        }

        // CASE 1: Doctor is selecting/buying a plan
        if ($doctor_id && $plan_id) {
            // SECURITY CHECK: Ensure doctor can only buy for themselves
            if ($auth && $auth->id && $auth->id != $doctor_id) {
                $this->response([
                    'success' => false,
                    'message' => 'Unauthorized: You can only select a plan for your own account.'
                ], REST_Controller::HTTP_UNAUTHORIZED);
                return;
            }

            $plan = $this->subscription_api_model->get_plan_details($plan_id);
            if (!$plan) {
                $this->response([
                    'success' => false,
                    'message' => 'Invalid Plan ID'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $data = [
                'type' => 'doctor',
                'doctor_id' => $doctor_id,
                'plan_id' => $plan_id,
                'duration' => $plan->duration_days,
                'amount' => $plan->price,
                'payment_id' => $this->post('payment_id') ?? ($stream_data['payment_id'] ?? null)
            ];

            $result = $this->subscription_api_model->buy_subscription($data);
            if ($result === 'already_active') {
                $this->response([
                    'success' => false,
                    'message' => 'You already have an active subscription for this plan.'
                ], REST_Controller::HTTP_BAD_REQUEST);
            } elseif ($result) {
                $this->response([
                    'success' => true,
                    'message' => 'Plan selected successfully',
                    'data' => $result
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'success' => false,
                    'message' => 'Failed to process subscription'
                ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
            return;
        }

        // CASE 2: Creating a new plan (Existing logic)
        $doctors_per_month = $this->post('doctors_per_month') ?? ($stream_data['doctors_per_month'] ?? null);
        $features = $this->post('features') ?? ($stream_data['features'] ?? null);
        $call_chat_number = $this->post('call_chat_number') ?? ($stream_data['call_chat_number'] ?? null);
        $whatsapp_chat_number = $this->post('whatsapp_chat_number') ?? ($stream_data['whatsapp_chat_number'] ?? null);
        $fair_usage_policy = $this->post('fair_usage_policy') ?? ($stream_data['fair_usage_policy'] ?? null);
        $is_active = $this->post('is_active') ?? ($stream_data['is_active'] ?? null);

        if (!$name || !$price || !$billing_cycle) {
            $this->response([
                'success' => false,
                'message' => 'Missing required fields (doctor_id, plan_id) or (name, price, billing_cycle)'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Extract duration days from billing cycle (e.g. "30 days" -> 30)
        $duration_days = (int)$billing_cycle;
        if (empty($duration_days)) {
            $duration_days = 30; // Default
        }

        $data = [
            'plan_type' => $type,
            'name' => $name,
            'price' => $price,
            'duration_days' => $duration_days,
            'max_doctors_allowed' => $doctors_per_month ?? 1,
            'call_chat' => $call_chat_number,
            'whatsapp_chat' => $whatsapp_chat_number,
            'fair_usage_policy' => $fair_usage_policy,
            'is_active' => $is_active ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Map features to perks/description
        if (is_array($features)) {
            $data['perks'] = implode(', ', $features);
            $data['description'] = implode(', ', $features);
        } else {
            $data['perks'] = $features;
            $data['description'] = $features;
        }

        $plan = $this->subscription_api_model->insert_plan($data);

        if ($plan) {
            $this->response([
                'success' => true,
                'message' => 'Doctor plan created successfully',
                'data' => [
                    'id' => (int)$plan->id,
                    'type' => $plan->plan_type,
                    'name' => $plan->name,
                    'price' => (float)$plan->price,
                    'billing_cycle' => $plan->duration_days . ' days',
                    'doctors_per_month' => (int)($plan->max_doctors_allowed ?? 1),
                    'features' => !empty($plan->description) ? array_map('trim', explode(',', $plan->description)) : [],
                    'call_chat_number' => $plan->call_chat,
                    'whatsapp_chat_number' => $plan->whatsapp_chat,
                    'fair_usage_policy' => $plan->fair_usage_policy,
                    'is_active' => $plan->is_active == 1,
                    'created_at' => date('c', strtotime($plan->created_at)),
                    'updated_at' => date('c', strtotime($plan->updated_at ?? $plan->created_at))
                ]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'success' => false,
                'message' => 'Failed to create doctor plan'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Get all doctors with active subscriptions
     * Matches user requested "doctor list" format
     */
    public function subscribed_doctors_list_post()
    {
        $user_id = $this->post('user_id');
        $plan_id = $this->post('plan_id');

        // Check Authentication if no IDs provided
        if (empty($user_id) && empty($plan_id)) {
            if (method_exists($this->common_model, 'auth')) {
                $auth = $this->common_model->auth();
                if ($auth && $auth->id && ($auth->type == 'user' || $auth->type == 'doctor')) {
                    $user_id = $auth->id;
                }
            }
        }

        // If user_id is provided but no plan_id, find their current active plan
        if ($user_id && empty($plan_id)) {
            $subscription = $this->subscription_api_model->get_my_subscription($user_id, 'customer');
            if ($subscription) {
                $plan_id = $subscription->plan_id;
            }
        }

        // Fetch doctors based on plan or global list, with exclusion for existing subscriptions
        if (!empty($plan_id)) {
            $doctors = $this->subscription_api_model->get_plan_doctors($plan_id, $user_id);
        } else {
            $doctors = $this->subscription_api_model->get_all_subscribed_doctors($user_id);
        }
        if ($doctors) {
            $this->response(['status' => 'success', 'data' => $doctors], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'No available doctors found for your plan.'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get details for a specific subscribed doctor
     * Matches user requested "subscribed doctor details" format
     */
    public function subscribed_doctor_details_post($id = NULL)
    {
        $doctor_id = $id ? $id : $this->post('doctor_id');
        if (empty($doctor_id)) {
            $this->response(['status' => false, 'message' => 'doctor_id is required'], REST_Controller::HTTP_OK);
            return;
        }

        $doctor = $this->subscription_api_model->get_subscribed_doctor_details($doctor_id);
        if ($doctor) {
            $this->response(['status' => true, 'data' => $doctor], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Doctor not found or not subscribed'], REST_Controller::HTTP_OK);
        }
    }
    /**
     * Get doctors assigned to specific featured plans (Classic, Advanced, Popular)
     * POST Param: plan_id (Optional)
     */
    public function plan_doctors_list_post()
    {
        $plan_id = $this->post('plan_id');
        $doctors = $this->subscription_api_model->get_plan_doctors($plan_id);
        
        if ($doctors) {
            $this->response(['status' => 'success', 'data' => $doctors], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'error', 'message' => 'No doctors found for this plan'], REST_Controller::HTTP_OK);
        }
    }
    /**
     * Get Subscription Terms
     * GET
     */
    public function subscription_terms_get() {
        $terms = $this->subscription_api_model->get_subscription_terms();
        if ($terms) {
            $this->response([
                'status' => 'success',
                'data' => $terms
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'No subscription terms found'
            ], REST_Controller::HTTP_OK);
        }
    }
}



