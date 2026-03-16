<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_top_listings_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_listings_by_month($month_key) {
        $this->db->select('dtl.*, d.doctor_name, d.hospital_name, d.mobile_number, dsp.name as plan_name');
        $this->db->from('doctor_top_listings dtl');
        $this->db->join('doctors d', 'dtl.doctor_id = d.id');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id AND ds.status = "active" AND ds.end_at > NOW()', 'left');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id', 'left');
        $this->db->where('dtl.month_key', $month_key);
        $this->db->order_by('dtl.position', 'ASC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_listing_by_id($id) {
        $this->db->select('dtl.*, d.doctor_name, d.hospital_name');
        $this->db->from('doctor_top_listings dtl');
        $this->db->join('doctors d', 'dtl.doctor_id = d.id');
        $this->db->where('dtl.id', $id);
        $result = $this->db->get()->row();
        return $result;
    }

    function insert_listing($data) {
        return $this->db->insert('doctor_top_listings', $data);
    }

    function update_listing($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('doctor_top_listings', $data);
    }

    function delete_listing($id) {
        $this->db->where('id', $id);
        return $this->db->delete('doctor_top_listings');
    }

    function get_listings_count($month_key) {
        $result = $this->db->where('month_key', $month_key)->get('doctor_top_listings')->num_rows();
        return $result;
    }

    function is_doctor_listed($month_key, $doctor_id) {
        $result = $this->db->where(array('month_key' => $month_key, 'doctor_id' => $doctor_id))->get('doctor_top_listings')->num_rows();
        return $result > 0;
    }

    function is_position_taken($month_key, $position) {
        $result = $this->db->where(array('month_key' => $month_key, 'position' => $position))->get('doctor_top_listings')->num_rows();
        return $result > 0;
    }

    function is_position_taken_by_other($month_key, $position, $exclude_id) {
        $this->db->where('month_key', $month_key);
        $this->db->where('position', $position);
        $this->db->where('id !=', $exclude_id);
        $result = $this->db->get('doctor_top_listings')->num_rows();
        return $result > 0;
    }

    function has_active_subscription($doctor_id) {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('status', 'active');
        $this->db->where('end_at >', date('Y-m-d H:i:s'));
        $result = $this->db->get('doctor_subscriptions')->num_rows();
        return $result > 0;
    }

    function get_available_doctors($month_key) {
        // Get ALL doctors with active subscriptions who are not already in the top listings
        // Use same validation as login API - only check doctor_login_status
        $this->db->select('d.id, d.doctor_name, d.hospital_name, d.mobile_number, dsp.name as plan_name, ds.end_at');
        $this->db->from('doctors d');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id AND ds.status = "active" AND ds.end_at > NOW()', 'inner');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id', 'left');
        $this->db->where('d.doctor_login_status', 'active');
        $this->db->where("d.id NOT IN (SELECT doctor_id FROM doctor_top_listings WHERE month_key = '$month_key')", NULL, FALSE);
        $this->db->order_by('d.doctor_name', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }

    function update_positions($month_key, $positions) {
        foreach ($positions as $listing_id => $position) {
            $this->db->where('id', $listing_id);
            $this->db->where('month_key', $month_key);
            $this->db->update('doctor_top_listings', array('position' => $position));
        }
        return true;
    }

    function get_monthly_listings($month_key) {
        $this->db->select('dtl.*, d.doctor_name, d.hospital_name, d.mobile_number, dsp.name as plan_name, 
                          CASE WHEN ds.status = "active" AND ds.end_at > NOW() THEN 1 ELSE 0 END as subscription_active');
        $this->db->from('doctor_top_listings dtl');
        $this->db->join('doctors d', 'dtl.doctor_id = d.id');
        $this->db->join('doctor_subscriptions ds', 'd.id = ds.doctor_id', 'left');
        $this->db->join('doctor_subscription_plans dsp', 'ds.doctor_subscription_plan_id = dsp.id', 'left');
        $this->db->where('dtl.month_key', $month_key);
        $this->db->order_by('dtl.position', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }
}
