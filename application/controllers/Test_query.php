<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_query extends CI_Controller {
    public function index() {
        $this->load->model('admin/Doctor_subscriptions_model', 'doctor_subscriptions_model');
        try {
            $user_id = $this->input->get('user_id');
            $status = $this->input->get('status');
            $subscriptions = $this->doctor_subscriptions_model->get_all_user_subscriptions($user_id, $status);
            echo "Query for subscriptions successful. Found " . count($subscriptions) . " rows.\n";
            
            $users = $this->db->get('users')->result();
            echo "Query for users successful. Found " . count($users) . " rows.\n";
            
            echo "All queries successful.\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
