<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db5 extends CI_Controller {
    public function index() {
        if ($this->db->table_exists('doctor_subscriptions')) {
            echo "doctor_subscriptions table exists\n";
            $fields = $this->db->list_fields('doctor_subscriptions');
            echo "Columns in doctor_subscriptions: " . implode(', ', $fields) . "\n";
        } else {
            echo "doctor_subscriptions table DOES NOT exist\n";
        }
    }
}
