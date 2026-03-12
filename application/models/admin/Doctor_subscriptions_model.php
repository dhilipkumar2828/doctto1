<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_subscriptions_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_subscriptions($doctor_id = null, $status = null) {
        $this->db->select('ds.*, d.doctor_name, d.hospital_name, d.mobile_number, dsp.name as plan_name, dsp.price as plan_price, dsp.description as plan_description');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'ds.doctor_id = d.id');
        $this->db->join('subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        
        if ($doctor_id) {
            $this->db->where('ds.doctor_id', $doctor_id);
        }
        
        if ($status) {
            $this->db->where('ds.status', $status);
        } else {
            // Only show valid statuses, hide empty junk records
            $this->db->where_in('ds.status', ['active', 'expired', 'cancelled', 'pending']);
        }

        // Only show records from 2026 onwards to hide 2025 test data
        $this->db->where('ds.start_at >=', '2026-01-01 00:00:00');
        
        $this->db->order_by('ds.id', 'DESC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_all_user_subscriptions($user_id = null, $status = null) {
        $this->db->select('us.*, u.first_name, u.last_name, u.phone, u.email, sp.name as plan_name, sp.price as plan_price, sp.description as plan_description, sp.max_doctors_allowed as consultations_remaining');
        $this->db->select('0 as consultations_used'); // Temporary fix since user_subscribed_doctors table does not exist
        $this->db->from('user_subscriptions us');
        $this->db->join('users u', 'us.user_id = u.id');
        $this->db->join('subscription_plans sp', 'us.plan_id = sp.id');
        
        if ($user_id) {
            $this->db->where('us.user_id', $user_id);
        }
        
        if ($status) {
            $this->db->where('us.status', $status);
        }
        
        $this->db->order_by('us.id', 'DESC');
        $query = $this->db->get();
        
        return $query ? $query->result() : [];
    }

    function get_subscription_by_id($id) {
        $this->db->select('ds.*, d.doctor_name, d.hospital_name, dsp.name as plan_name');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'ds.doctor_id = d.id');
        $this->db->join('subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id', 'left');
        $this->db->where('ds.id', $id);
        $result = $this->db->get()->row();
        return $result;
    }

    function insert_subscription($data) {
        return $this->db->insert('doctor_subscriptions', $data);
    }

    function update_subscription($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('doctor_subscriptions', $data);
    }

    function change_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('doctor_subscriptions', array('status' => $status));
    }

    function get_plan_by_id($id) {
        $result = $this->db->where('id', $id)->get('subscription_plans')->row();
        return $result;
    }

    function get_active_plans() {
        $this->db->where('plan_type', 'doctor');
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        $result = $this->db->get('subscription_plans')->result();
        return $result;
    }

    function get_doctor_active_subscription($doctor_id) {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('status', 'active');
        $this->db->where('end_at >', date('Y-m-d H:i:s'));
        $result = $this->db->get('doctor_subscriptions')->row();
        return $result;
    }

    function get_active_subscription($doctor_id) {
        $this->db->select('ds.*, dsp.name as plan_name, dsp.price as plan_price');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id', 'left');
        $this->db->where('ds.doctor_id', $doctor_id);
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.end_at >', date('Y-m-d H:i:s'));
        $result = $this->db->get()->row();
        return $result;
    }

    function change_featured_status($id, $status) {
        $sub = $this->get_subscription_by_id($id);
        if ($sub) {
            $this->db->where('doctor_id', $sub->doctor_id);
            $this->db->where('status', 'active');
            return $this->db->update('doctor_subscriptions', array('featured_status' => $status));
        }
        return false;
    }
}
