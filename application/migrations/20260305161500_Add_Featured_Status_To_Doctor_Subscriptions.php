<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Featured_Status_To_Doctor_Subscriptions extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('featured_status', 'doctor_subscriptions')) {
            $fields = array(
                'featured_status' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'status'
                )
            );
            $this->dbforge->add_column('doctor_subscriptions', $fields);
        }
    }

    public function down() {
        if ($this->db->field_exists('featured_status', 'doctor_subscriptions')) {
            $this->dbforge->drop_column('doctor_subscriptions', 'featured_status');
        }
    }
}
