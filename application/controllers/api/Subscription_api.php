<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Subscription_api extends REST_Controller {

    public function __construct() {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers,Authorization,Access-Control-Allow-Origin,Access-Control-Allow-Methods");
        parent::__construct();
        $this->load->model('subscription_api_model');
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
     * Get active subscription for a specific user/doctor
     * POST Params: id, type (doctor/customer)
     */
    public function my_subscription_post() {
        $id = $this->post('id');
        $type = $this->post('type') ? $this->post('type') : $this->post('plan_type');

        if (!$id || !$type) {
            $this->response(['status' => 'error', 'message' => 'Missing ID or Type'], REST_Controller::HTTP_OK);
            return;
        }

        $subscription = $this->subscription_api_model->get_my_subscription($id, $type);
        if ($subscription) {
            $this->response([
                'status' => 'success',
                'data' => $subscription
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 'error',
                'message' => 'No active subscription found'
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Record a new subscription purchase
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
            'payment_id' => $payment_id
        ];

        if ($type == 'doctor') {
            $data['doctor_id'] = $id;
        } else {
            $data['user_id'] = $id;
        }

        $result = $this->subscription_api_model->buy_subscription($data);
        if ($result) {
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
}
