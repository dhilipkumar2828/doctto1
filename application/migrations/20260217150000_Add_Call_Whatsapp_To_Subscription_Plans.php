<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Call_Whatsapp_To_Subscription_Plans extends CI_Migration {

    public function up() {
        $fields = array(
            'call_chat' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'duration_days'
            ),
            'whatsapp_chat' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'call_chat'
            )
        );
        
        // Add columns to subscription_plans table if they don't exist
        if ($this->db->table_exists('subscription_plans')) {
            if (!$this->db->field_exists('call_chat', 'subscription_plans')) {
                $this->dbforge->add_column('subscription_plans', $fields);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('subscription_plans')) {
            $this->dbforge->drop_column('subscription_plans', 'call_chat');
            $this->dbforge->drop_column('subscription_plans', 'whatsapp_chat');
        }
    }
}
