<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Plans extends CI_Migration {

    public function up() {
        // Customer Subscription Plans
        if (!$this->db->table_exists('subscription_plans')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255),
                'description' => array('type' => 'TEXT', 'null' => TRUE),
                'price' => array('type' => 'DECIMAL', 'constraint' => '10,2'),
                'duration_days' => array('type' => 'INT', 'constraint' => 11),
                'max_doctors_allowed' => array('type' => 'INT', 'constraint' => 11, 'default' => 1),
                'total_consultations' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'is_active' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
                'created_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'),
                'updated_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP')
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('subscription_plans');
        }

        // doctor_subscription_plans
        if (!$this->db->table_exists('doctor_subscription_plans')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255),
                'description' => array('type' => 'TEXT', 'null' => TRUE),
                'price' => array('type' => 'DECIMAL', 'constraint' => '10,2'),
                'duration_days' => array('type' => 'INT', 'constraint' => 11),
                'perks' => array('type' => 'TEXT', 'null' => TRUE),
                'is_active' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
                'created_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'),
                'updated_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP', 'on_update' => 'CURRENT_TIMESTAMP')
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('doctor_subscription_plans');
        }

        // Insert/Update Customer Plans
        $customer_plans = array(
            array('id' => 1, 'name' => 'Classic Plan', 'price' => 100.00, 'duration_days' => 30, 'max_doctors_allowed' => 1, 'description' => '1 Doctor / Month, Chat, Audio & Video Consultations, 24/7 Access, Connect faster & easier'),
            array('id' => 2, 'name' => 'Advanced Plan', 'price' => 250.00, 'duration_days' => 30, 'max_doctors_allowed' => 3, 'description' => '3 Doctors / Month, Chat, Audio & Video Consultations, 24/7 Access, Connect faster & easier'),
            array('id' => 3, 'name' => 'Popular Plan', 'price' => 500.00, 'duration_days' => 30, 'max_doctors_allowed' => 5, 'description' => '5 Doctors / Month, Chat, Audio & Video Consultations, 24/7 Access, Connect faster & easier')
        );

        foreach ($customer_plans as $plan) {
            $exists = $this->db->get_where('subscription_plans', array('id' => $plan['id']))->row();
            if ($exists) {
                $this->db->where('id', $plan['id'])->update('subscription_plans', $plan);
            } else {
                $this->db->insert('subscription_plans', $plan);
            }
        }

        // Insert/Update Doctor Plans
        $doctor_plans = array(
            array('id' => 1, 'name' => 'Classic Plan', 'price' => 100.00, 'duration_days' => 30, 'perks' => "Random Profile Listing\nLimited Access"),
            array('id' => 2, 'name' => 'Advanced Plan', 'price' => 499.00, 'duration_days' => 30, 'perks' => "Advanced Profile Visibility\nMore Access\nAdvanced Rating Boost\nHighlighted Profile"),
            array('id' => 3, 'name' => 'Popular Plan', 'price' => 999.00, 'duration_days' => 30, 'perks' => "Popular Profile Visibility\nHigh Access\nHigh Rated Priority\nHigh Views\nPR Articles")
        );

        foreach ($doctor_plans as $plan) {
            $exists = $this->db->get_where('doctor_subscription_plans', array('id' => $plan['id']))->row();
            if ($exists) {
                $this->db->where('id', $plan['id'])->update('doctor_subscription_plans', $plan);
            } else {
                $this->db->insert('doctor_subscription_plans', $plan);
            }
        }
    }

    public function down() {
        // $this->dbforge->drop_table('subscription_plans');
        // $this->dbforge->drop_table('doctor_subscription_plans');
    }
}
