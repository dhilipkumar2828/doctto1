<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Align_Doctor_Plans extends CI_Migration {

    public function up() {
        // 1. Add max_doctors_allowed to doctor_subscription_plans
        if (!$this->db->field_exists('max_doctors_allowed', 'doctor_subscription_plans')) {
            $fields = array(
                'max_doctors_allowed' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1,
                    'after' => 'duration_days'
                )
            );
            $this->dbforge->add_column('doctor_subscription_plans', $fields);
        }

        // 2. Update the default limits
        $this->db->where('id', 1)->update('doctor_subscription_plans', array('max_doctors_allowed' => 1)); // Classic
        $this->db->where('id', 2)->update('doctor_subscription_plans', array('max_doctors_allowed' => 3)); // Advanced
        $this->db->where('id', 3)->update('doctor_subscription_plans', array('max_doctors_allowed' => 5)); // Popular
    }

    public function down() {
        if ($this->db->field_exists('max_doctors_allowed', 'doctor_subscription_plans')) {
            $this->dbforge->drop_column('doctor_subscription_plans', 'max_doctors_allowed');
        }
    }
}
