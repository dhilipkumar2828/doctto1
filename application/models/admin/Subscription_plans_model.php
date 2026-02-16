<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_plans_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_plans() {
        $this->db->select('sp.*, COUNT(spd.doctor_id) as assigned_doctors_count');
        $this->db->from('subscription_plans sp');
        $this->db->join('subscription_plan_doctors spd', 'sp.id = spd.plan_id', 'left');
        $this->db->group_by('sp.id');
        $this->db->order_by('sp.id', 'DESC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_plan_by_id($id) {
        $result = $this->db->where('id', $id)->get('subscription_plans')->row();
        return $result;
    }

    function insert_plan($data) {
        $this->db->insert('subscription_plans', $data);
        return $this->db->insert_id();
    }

    function update_plan($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('subscription_plans', $data);
    }

    function change_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('subscription_plans', array('is_active' => $status));
    }

    function get_plan_features($plan_id) {
        $this->db->select('spf.*, cf.title, cf.price');
        $this->db->from('subscription_plan_features spf');
        $this->db->join('consultation_fees cf', 'spf.consultation_fees_id = cf.id');
        $this->db->where('spf.plan_id', $plan_id);
        $result = $this->db->get()->result();
        return $result;
    }

    function insert_plan_feature($data) {
        return $this->db->insert('subscription_plan_features', $data);
    }

    function delete_plan_features($plan_id) {
        $this->db->where('plan_id', $plan_id);
        return $this->db->delete('subscription_plan_features');
    }

    function get_assigned_doctors($plan_id) {
        $this->db->select('spd.*, d.doctor_name, d.hospital_name, d.mobile_number');
        $this->db->from('subscription_plan_doctors spd');
        $this->db->join('doctors d', 'spd.doctor_id = d.id');
        $this->db->where('spd.plan_id', $plan_id);
        $this->db->order_by('spd.sort_order', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }

    function get_available_doctors($plan_id) {
        // Use same validation as login API - only check doctor_login_status
        $this->db->select('d.id, d.doctor_name, d.hospital_name, d.mobile_number');
        $this->db->from('doctors d');
        $this->db->where('d.doctor_login_status', 'active');
        $this->db->where("d.id NOT IN (SELECT doctor_id FROM subscription_plan_doctors WHERE plan_id = $plan_id)", NULL, FALSE);
        $result = $this->db->get()->result();
        return $result;
    }

    function get_assigned_doctors_count($plan_id) {
        $result = $this->db->where('plan_id', $plan_id)->get('subscription_plan_doctors')->num_rows();
        return $result;
    }

    function get_plan_max_doctors($plan_id) {
        $result = $this->db->select('max_doctors_allowed')->where('id', $plan_id)->get('subscription_plans')->row();
        return $result ? $result->max_doctors_allowed : 0;
    }

    function is_doctor_assigned($plan_id, $doctor_id) {
        $result = $this->db->where(array('plan_id' => $plan_id, 'doctor_id' => $doctor_id))->get('subscription_plan_doctors')->num_rows();
        return $result > 0;
    }

    function assign_doctor($data) {
        return $this->db->insert('subscription_plan_doctors', $data);
    }

    function remove_doctor($plan_id, $doctor_id) {
        $this->db->where(array('plan_id' => $plan_id, 'doctor_id' => $doctor_id));
        return $this->db->delete('subscription_plan_doctors');
    }

    function update_doctor_order($plan_id, $doctor_orders) {
        foreach ($doctor_orders as $doctor_id => $sort_order) {
            $this->db->where(array('plan_id' => $plan_id, 'doctor_id' => $doctor_id));
            $this->db->update('subscription_plan_doctors', array('sort_order' => $sort_order));
        }
        return true;
    }

    function get_active_plans() {
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        $result = $this->db->get('subscription_plans')->result();
        return $result;
    }
}
