<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_manage_category_model extends CI_Model {


    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    function get_doctor_category($doctor_id){
          // $qry = $this->db->query("select * from doctor_admin_comission where doctor_id=$doctor_id");
            $this->db->where('doctor_id',$doctor_id);
            $qry = $this->db->get('doctor_admin_comission');
        if ($qry->num_rows() > 0) {
            $result = $qry->result();

            //get the category name
            foreach ($result as $row) {
                $row->category_name = $this->db->get_where('doctor_categories',['id'=>$row->cat_id])->row()->category_name;
            }

          
            return $result;
        }
        else
        {
            return FALSE;
        }
    }


    function get_doctor_symptoms($doctor_id){
          // $qry = $this->db->query("select * from doctor_admin_comission where doctor_id=$doctor_id");
        $this->db->where('doctor_id',$doctor_id);
        $qry = $this->db->get('doctor_symptoms_subsymptoms');
        if($qry->num_rows() > 0) 
        {
            $result = $qry->result();
            foreach($result as $res) {
               $res->symptom_name = ($this->db->where('id', $res->symptom_id)->get('symptom')->row())->name;
               $res->sub_symptom_ids = explode(',',$res->sub_symptom_id);
            }
            
            return $result;
        }

        else
        {
            return FALSE;
        }
    }
    
      function get_doctor_sub_symptoms($cid){

        $this->db->where('symptom_id',$cid);
        $qry = $this->db->get('doctor_symptoms_subsymptoms');
        if($qry->num_rows() > 0) 
        {
            $result = $qry->result();

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

     function get_doctor_commision($id){
          // $qry = $this->db->query("select * from doctor_admin_comission where doctor_id=$doctor_id");
            $this->db->where('id',$id);
            $qry = $this->db->get('doctor_admin_comission');
        if ($qry->num_rows() > 0) {
            $result = $qry->row();
            return $result;
        }
        else
        {
            return FALSE;
        }
    }


    function insert($doctor_id, $Category)
    {
        $array=array('cat_id'=>$Category,'doctor_id'=>$doctor_id);
        $ins = $this->db->insert("doctor_admin_comission",$array);
        if($ins)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


  function get_doctor_manage_category($id,$data)
    {
        $this->db->where('id',$id);
       $chk = $this->db->update('doctor_admin_comission',$data);
       return $chk;
    }

    function getSymptoms()
    {
      $data = $this->db->where(array('status'=>1))->get('symptom')->result();
      if(count($data)>0)
      {
        return $data;
      }
      else
      {
        return array();
      }
    }

    
   function delete($parameters) {

         $this->db->where('id',$parameters['id']);
          $this->db->delete('doctor_admin_comission');
          if($this->db->affected_rows() > 0){
          return true;
          } 


} 
}



?>