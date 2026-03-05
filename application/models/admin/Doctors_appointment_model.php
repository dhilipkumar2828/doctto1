<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Doctors_appointment_model extends CI_Model {


    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    function get_appointment() 
    {
        // Get offline appointments
        $this->db->select("*, 'offline' as source");
        $this->db->order_by("id","desc");
        $offline = $this->db->get("doctor_appointments")->result_array();
        
        // Get completed online appointments
        $this->db->select("*, 'online' as source, type as appointment_type, payment_status as doctor_status, '' as rejected_by, '' as reason, '' as comments");
        $this->db->where('payment_status', 'completed');
        $this->db->order_by("id","desc");
        $online = $this->db->get("online_doctor_appointments")->result_array();
        
        $merged = array_merge($offline, $online);
        
        // Convert back to object result like CI's result()
        $result = array();
        foreach($merged as $row) {
            $result[] = (object) $row;
        }
        
        return !empty($result) ? $result : FALSE;
    }

    function get_online_appointments($status = null) 
    {
        $this->db->select('oa.*, u.first_name as user_name, d.doctor_name, d.hospital_name');
        $this->db->from('online_doctor_appointments oa');
        $this->db->join('users u', 'oa.patient_id = u.id', 'left');
        $this->db->join('doctors d', 'oa.doctor_id = d.id', 'left');
        
        if($status == 'completed') {
            $this->db->where('oa.payment_status', 'completed');
        }
        
        $this->db->order_by("oa.id","desc");
        $qry = $this->db->get();
        
        return $qry->result();
    }
    
    function search($start_date, $end_date) {
        // Search offline
        $this->db->select("*, 'offline' as source");
        $this->db->order_by("id", "desc");
        $where = "(date BETWEEN '".$start_date."' AND '".$end_date."')";
        $this->db->where($where);
        $offline = $this->db->get('doctor_appointments')->result_array();
        
        // Search online
        $this->db->select("*, 'online' as source, type as appointment_type, payment_status as doctor_status, '' as rejected_by, '' as reason, '' as comments");
        $this->db->where('payment_status', 'completed');
        $this->db->order_by("id", "desc");
        $where = "(date BETWEEN '".$start_date."' AND '".$end_date."')";
        $this->db->where($where);
        $online = $this->db->get('online_doctor_appointments')->result_array();
        
        $merged = array_merge($offline, $online);
        
        $result = array();
        foreach($merged as $row) {
            $result[] = (object) $row;
        }
        
        return $result;
    }

    function search_online($start_date, $end_date) {
        $this->db->select('oa.*, u.first_name as user_name, d.doctor_name, d.hospital_name');
        $this->db->from('online_doctor_appointments oa');
        $this->db->join('users u', 'oa.patient_id = u.id', 'left');
        $this->db->join('doctors d', 'oa.doctor_id = d.id', 'left');
        $this->db->order_by("oa.id", "desc");
        $where = "(oa.date BETWEEN '".$start_date."' AND '".$end_date."')";
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }
       
}

?>
