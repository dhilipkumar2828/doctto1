<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Doctors_appointment_model extends CI_Model {


    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    function get_appointment() 
    {
        $this->db->order_by("id","desc");
        $qry = $this->db->get("doctor_appointments");
        
        if ($qry->num_rows() > 0) {
            $result = $qry->result();
            return $result;
        }
        else
        {
            return FALSE;
        }
    }
    
    function search($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('doctor_appointments');
       $this->db->order_by("id", "desc");
        $where = "(date BETWEEN '".$start_date."' AND '".$end_date."')";
        $this->db->where($where);
        $query = $this->db->get();
        return $result = $query->result();
    }

       
}

?>