<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Terms_conditions_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_terms($plan_type = null, $status = null) {
        $this->db->select('stc.*, COUNT(ts.id) as sections_count');
        $this->db->from('subscription_terms_conditions stc');
        $this->db->join('terms_sections ts', 'stc.id = ts.terms_id', 'left');
        
        if ($plan_type) {
            $this->db->where('stc.plan_type', $plan_type);
        }
        
        if ($status !== null) {
            $this->db->where('stc.is_active', $status);
        }
        
        $this->db->group_by('stc.id');
        $this->db->order_by('stc.created_at', 'DESC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_terms_by_id($id) {
        $this->db->where('id', $id);
        $result = $this->db->get('subscription_terms_conditions')->row();
        return $result;
    }

    function get_active_terms($plan_type, $subscription_plan_id = null) {
        $this->db->where('is_active', 1);
        $this->db->where('plan_type', $plan_type);
        
        if ($subscription_plan_id) {
            $this->db->where('(subscription_plan_id = ' . $subscription_plan_id . ' OR subscription_plan_id IS NULL)');
        }
        
        $this->db->order_by('effective_date', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get('subscription_terms_conditions')->row();
        return $result;
    }

    function insert_terms($data) {
        return $this->db->insert('subscription_terms_conditions', $data);
    }

    function update_terms($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('subscription_terms_conditions', $data);
    }

    function change_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('subscription_terms_conditions', array('is_active' => $status));
    }

    function delete_terms($id) {
        // First delete all sections
        $this->db->where('terms_id', $id);
        $this->db->delete('terms_sections');
        
        // Then delete the terms
        $this->db->where('id', $id);
        return $this->db->delete('subscription_terms_conditions');
    }

    function get_terms_sections($terms_id) {
        $this->db->where('terms_id', $terms_id);
        $this->db->order_by('section_order', 'ASC');
        $result = $this->db->get('terms_sections')->result();
        return $result;
    }

    function get_section_by_id($section_id) {
        $this->db->where('id', $section_id);
        $result = $this->db->get('terms_sections')->row();
        return $result;
    }

    function insert_section($data) {
        return $this->db->insert('terms_sections', $data);
    }

    function update_section($section_id, $data) {
        $this->db->where('id', $section_id);
        return $this->db->update('terms_sections', $data);
    }

    function delete_section($section_id) {
        $this->db->where('id', $section_id);
        return $this->db->delete('terms_sections');
    }

    function get_terms_with_sections($plan_type, $subscription_plan_id = null) {
        // Get active terms
        $terms = $this->get_active_terms($plan_type, $subscription_plan_id);
        
        if ($terms) {
            // Get sections for these terms
            $sections = $this->get_terms_sections($terms->id);
            $terms->sections = $sections;
        }
        
        return $terms;
    }

    function log_terms_acceptance($user_id, $user_type, $terms_id, $subscription_id = null, $ip_address = null, $user_agent = null) {
        $data = array(
            'user_id' => $user_id,
            'user_type' => $user_type,
            'terms_id' => $terms_id,
            'subscription_id' => $subscription_id,
            'ip_address' => $ip_address ?: '127.0.0.1',
            'user_agent' => $user_agent ?: 'API Request'
        );
        
        return $this->db->insert('terms_acceptance_log', $data);
    }

    function check_terms_acceptance($user_id, $user_type, $terms_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $user_type);
        $this->db->where('terms_id', $terms_id);
        
        $result = $this->db->get('terms_acceptance_log')->row();
        return $result ? true : false;
    }
}
