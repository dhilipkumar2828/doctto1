<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_subscription_plans_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all_plans() {
        $this->db->select('dsp.*, COUNT(ds.id) as active_subscriptions_count');
        $this->db->from('doctor_subscription_plans dsp');
        $this->db->join('doctor_subscriptions ds', 'dsp.id = ds.doctor_subscription_plan_id AND ds.status = "active"', 'left');
        $this->db->group_by('dsp.id');
        $this->db->order_by('dsp.id', 'DESC');
        $result = $this->db->get()->result();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }

    function get_plan_by_id($id) {
        $result = $this->db->where('id', $id)->get('doctor_subscription_plans')->row();
        if ($result && isset($result->perks)) {
            $result->perks = str_replace(['\r\n', '\n', '\r'], "\n", $result->perks);
        }
        return $result;
    }

    function insert_plan($data) {
        return $this->db->insert('doctor_subscription_plans', $data);
    }

    function update_plan($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('doctor_subscription_plans', $data);
    }

    function change_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('doctor_subscription_plans', array('is_active' => $status));
    }

    function get_active_plans() {
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        $result = $this->db->get('doctor_subscription_plans')->result();
        return $result;
    }
}
