<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Columns_To_Subscription_Plan_Doctors extends CI_Migration {

    public function up() {
        // First, check if the table exists. If not, create it.
        if (!$this->db->table_exists('subscription_plan_doctors')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'plan_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'doctor_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'is_default' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
                'sort_order' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'created_at' => array('type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP')
            ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('plan_id');
            $this->dbforge->add_key('doctor_id');
            $this->dbforge->create_table('subscription_plan_doctors');
        } else {
            // Table exists, check for missing columns
            $fields = array();
            
            if (!$this->db->field_exists('is_default', 'subscription_plan_doctors')) {
                $fields['is_default'] = array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'doctor_id');
            }
            
            if (!$this->db->field_exists('sort_order', 'subscription_plan_doctors')) {
                $fields['sort_order'] = array('type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'is_default');
            }

            if (!empty($fields)) {
                $this->dbforge->add_column('subscription_plan_doctors', $fields);
            }
        }
    }

    public function down() {
        // Usually we don't drop columns in down unless necessary
    }
}
