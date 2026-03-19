<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_plans_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_plans() {
        $this->db->select('sp.*, COUNT(spd.doctor_id) as assigned_doctors_count, cp.max_doctors_allowed as customer_max_doctors');
        $this->db->from('subscription_plans sp');
        $this->db->join('subscription_plan_doctors spd', 'sp.id = spd.plan_id', 'left');
        $this->db->join('subscription_plans cp', 'sp.name = cp.name AND cp.plan_type = "customer"', 'left');
        $this->db->where('sp.plan_type', 'doctor');
        $this->db->group_by('sp.id');
        $this->db->order_by('sp.id', 'ASC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            foreach ($result as $row) {
                if (isset($row->customer_max_doctors) && $row->customer_max_doctors !== NULL) {
                    $row->max_doctors_allowed = $row->customer_max_doctors;
                }
            }
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_plan_by_id($id) {
        $this->db->select('sp.*, cp.max_doctors_allowed as customer_max_doctors');
        $this->db->from('subscription_plans sp');
        $this->db->join('subscription_plans cp', 'sp.name = cp.name AND cp.plan_type = "customer"', 'left');
        $this->db->where(['sp.id' => $id, 'sp.plan_type' => 'doctor']);
        $result = $this->db->get()->row();
        if ($result && isset($result->customer_max_doctors) && $result->customer_max_doctors !== NULL) {
            $result->max_doctors_allowed = $result->customer_max_doctors;
        }
        return $result;
    }

    function insert_plan($data) {
        $data['plan_type'] = 'doctor';
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
        $this->db->select('spd.*, d.doctor_name, d.hospital_name, d.mobile_number, sp.name as plan_name');
        $this->db->from('subscription_plan_doctors spd');
        $this->db->join('doctors d', 'spd.doctor_id = d.id');
        $this->db->join('subscription_plans sp', 'spd.plan_id = sp.id');
        $this->db->where('spd.plan_id', $plan_id);
        $this->db->order_by('spd.sort_order', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }

    function get_all_assigned_doctors() {
        $this->db->select('spd.*, d.doctor_name, d.hospital_name, d.mobile_number, sp.name as plan_name');
        $this->db->from('subscription_plan_doctors spd');
        $this->db->join('doctors d', 'spd.doctor_id = d.id');
        $this->db->join('subscription_plans sp', 'spd.plan_id = sp.id');
        $this->db->order_by('sp.name', 'ASC');
        $this->db->order_by('spd.sort_order', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }

    function get_available_doctors($plan_id) {
        // Get the current plan name
        $plan = $this->get_plan_by_id($plan_id);
        if (!$plan) return [];
        $plan_name = $plan->name;

        // Get all possible IDs for this plan name from both plan tables
        $this->db->select('id');
        $this->db->where('name', $plan_name);
        $ids1 = $this->db->get('doctor_subscription_plans')->result_array();
        
        $this->db->select('id');
        $this->db->where('name', $plan_name);
        $this->db->where('plan_type', 'doctor');
        $ids2 = $this->db->get('subscription_plans')->result_array();
        
        $all_ids = array_unique(array_merge(
            array_column($ids1, 'id'), 
            array_column($ids2, 'id')
        ));

        if (empty($all_ids)) {
            return [];
        }

        $this->db->select('d.id, d.doctor_name, d.hospital_name, d.mobile_number');
        $this->db->from('doctors d');
        // Join with doctor_subscriptions to find doctors who subscribed to this plan (by name match)
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id');
        $this->db->where_in('ds.doctor_subscription_plan_id', $all_ids);
        $this->db->where('ds.status', 'active');
        // Use correct column name: doctor_show_status (not doctor_login_status)
        $this->db->where('d.doctor_show_status', 'active');
        
        // Exclude doctors who are already assigned to THIS specific plan
        $this->db->where("d.id NOT IN (SELECT doctor_id FROM subscription_plan_doctors WHERE plan_id = $plan_id)", NULL, FALSE);
        
        $this->db->group_by('d.id');
        $result = $this->db->get()->result();
        return $result;
    }

    function get_all_available_doctors_for_assignments() {
        // This gets all active doctors who have an active subscription 
        // and can be assigned to their respective plan lists.
        $this->db->select('d.id, d.doctor_name, d.hospital_name, d.mobile_number, sp.id as plan_id, sp.name as plan_name');
        $this->db->from('doctors d');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id');
        // We match with subscription_plans to get the target mapping
        // This model assumes there's a link between the subscription and the manage list
        $this->db->join('subscription_plans sp', 'ds.doctor_subscription_plan_id = sp.id OR ds.doctor_subscription_plan_id IN (SELECT id FROM doctor_subscription_plans WHERE name = sp.name)');
        $this->db->where('ds.status', 'active');
        $this->db->where('d.doctor_show_status', 'active');
        $this->db->where('sp.plan_type', 'doctor');
        
        // Exclude already assigned pairs
        $this->db->where("NOT EXISTS (SELECT 1 FROM subscription_plan_doctors spd WHERE spd.doctor_id = d.id AND spd.plan_id = sp.id)", NULL, FALSE);
        
        $result = $this->db->get()->result();
        return $result;
    }

    function get_assigned_doctors_count($plan_id) {
        $result = $this->db->where('plan_id', $plan_id)->get('subscription_plan_doctors')->num_rows();
        return $result;
    }

    function get_plan_max_doctors($plan_id) {
        $plan = $this->get_plan_by_id($plan_id);
        return $plan ? $plan->max_doctors_allowed : 0;
    }

    function is_doctor_assigned($plan_id, $doctor_id) {
        $result = $this->db->where(array('plan_id' => $plan_id, 'doctor_id' => $doctor_id))->get('subscription_plan_doctors')->num_rows();
        return $result > 0;
    }

        function is_doctor_assigned_to_any_plan($doctor_id) {
        $result = $this->db->where('doctor_id', $doctor_id)->get('subscription_plan_doctors')->num_rows();
        return $result > 0;
    }

    function assign_doctor($data) {
        return $this->db->insert('subscription_plan_doctors', $data);
    }

    function remove_doctor($plan_id, $doctor_id = NULL) {
        $this->db->where('plan_id', $plan_id);
        if ($doctor_id) {
            $this->db->where('doctor_id', $doctor_id);
        }
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
        $this->db->select('sp.*, cp.max_doctors_allowed as customer_max_doctors');
        $this->db->from('subscription_plans sp');
        $this->db->join('subscription_plans cp', 'sp.name = cp.name AND cp.plan_type = "customer"', 'left');
        $this->db->where('sp.plan_type', 'doctor');
        $this->db->where('sp.is_active', 1);
        $this->db->order_by('sp.id', 'ASC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            foreach ($result as $row) {
                if (isset($row->customer_max_doctors) && $row->customer_max_doctors !== NULL) {
                    $row->max_doctors_allowed = $row->customer_max_doctors;
                }
            }
        }
        return $result;
    }
    function get_plan_doctor($plan_id, $doctor_id) {
        $this->db->select('spd.*, d.doctor_name, sp.name as plan_name');
        $this->db->from('subscription_plan_doctors spd');
        $this->db->join('doctors d', 'spd.doctor_id = d.id');
        $this->db->join('subscription_plans sp', 'spd.plan_id = sp.id');
        $this->db->where(['spd.plan_id' => $plan_id, 'spd.doctor_id' => $doctor_id]);
        return $this->db->get()->row();
    }

    function update_plan_doctor($plan_id, $doctor_id, $data) {
        $this->db->where(['plan_id' => $plan_id, 'doctor_id' => $doctor_id]);
        return $this->db->update('subscription_plan_doctors', $data);
    }
}
