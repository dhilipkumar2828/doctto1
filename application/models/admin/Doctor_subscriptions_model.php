<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_subscriptions_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_subscriptions($doctor_id = null, $status = null) {
        $this->db->select('ds.*, d.doctor_name, d.hospital_name, d.mobile_number, dsp.name as plan_name, dsp.price as plan_price');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'ds.doctor_id = d.id');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        
        if ($doctor_id) {
            $this->db->where('ds.doctor_id', $doctor_id);
        }
        
        if ($status) {
            $this->db->where('ds.status', $status);
        }
        
        $this->db->order_by('ds.created_at', 'DESC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_all_user_subscriptions($user_id = null, $status = null) {
        $this->db->select('us.*, u.first_name, u.last_name, u.phone, sp.name as plan_name, sp.price as plan_price');
        $this->db->from('user_subscriptions us');
        $this->db->join('users u', 'us.user_id = u.id');
        $this->db->join('subscription_plans sp', 'us.plan_id = sp.id');
        
        if ($user_id) {
            $this->db->where('us.user_id', $user_id);
        }
        
        if ($status) {
            $this->db->where('us.status', $status);
        }
        
        $this->db->order_by('us.created_at', 'DESC');
        $query = $this->db->get();
        
        return $query ? $query->result() : [];
    }

    function get_subscription_by_id($id) {
        $this->db->select('ds.*, d.doctor_name, d.hospital_name, dsp.name as plan_name');
        $this->db->from('doctor_subscriptions ds');
        $this->db->join('doctors d', 'ds.doctor_id = d.id');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
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
        $result = $this->db->where('id', $id)->get('doctor_subscription_plans')->row();
        return $result;
    }

    function get_active_plans() {
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        $result = $this->db->get('doctor_subscription_plans')->result();
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
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id');
        $this->db->where('ds.doctor_id', $doctor_id);
        $this->db->where('ds.status', 'active');
        $this->db->where('ds.end_at >', date('Y-m-d H:i:s'));
        $result = $this->db->get()->row();
        return $result;
    }
}
