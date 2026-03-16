<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db4 extends CI_Controller {
    public function index() {
        if ($this->db->table_exists('doctor_subscription_plans')) {
            echo "doctor_subscription_plans table exists\n";
            $fields = $this->db->list_fields('doctor_subscription_plans');
            echo "Columns in doctor_subscription_plans: " . implode(', ', $fields) . "\n";
        } else {
            echo "doctor_subscription_plans table DOES NOT exist\n";
        }
    }
}
