<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db3 extends CI_Controller {
    public function index() {
        $fields = $this->db->list_fields('subscription_plans');
        echo "Columns in subscription_plans: " . implode(', ', $fields) . "\n";
    }
}
