<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_db extends CI_Controller {
    public function index() {
        $count = $this->db->count_all('users');
        echo "Users count: " . $count . "\n";
        
        $count_sub = $this->db->count_all('user_subscriptions');
        echo "User subscriptions count: " . $count_sub . "\n";

        // Check columns of user_subscriptions
        $fields = $this->db->list_fields('user_subscriptions');
        echo "Columns in user_subscriptions: " . implode(', ', $fields) . "\n";
        
        // Check if subscription_plans exists
        if ($this->db->table_exists('subscription_plans')) {
            echo "subscription_plans table exists\n";
        } else {
            echo "subscription_plans table DOES NOT exist\n";
        }
    }
}
