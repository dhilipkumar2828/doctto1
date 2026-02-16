<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Ads_model extends CI_Model {


    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

     function get_doctors() 
    {
        $data = $this->db->get("ads")->result();
        if(!empty($data))
        {
            return $data;
        }
        else
        {
            return FALSE;
        }
    }


    function add($data) {

        $this->db->set($data);
        $this->db->insert('ads');
        if ($this->db->insert_id()) {
            return $this->db->insert_id();
        }
    }


        function insertData($title,$appimage,$banner_type,$category_id,$doctor_id)
        {

        $array=array('title'=>$title,'app_image'=>$appimage,'category_id'=>$category_id,'doctor_id'=>$doctor_id,'banner_type'=>$banner_type);
        $ins = $this->db->insert("ads",$array);
        //echo $this->db->last_query(); die;
        if($ins)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


  function updateData($title,$appimage,$banner_type,$category_id,$doctor_id)
    {
        $array=array('title'=>$title,'appimage'=>$appimage,'category_id'=>$category_id,'doctor_id'=>$doctor_id,'banner_type'=>$banner_type);
        $where = array('id'=>$id);
        $ins = $this->db->update("ads",$array,$where);
        if($ins)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function get_doctorby_id($id){
        $this->db->select('*');
        $this->db->from('ads');
         $this->db->where('id',$id);
        $qry = $this->db->get();
        $row = $qry->row();
        return $row;

    }
    function delete($parameters) {
         $this->db->where('id',$parameters['id']);
          $this->db->delete('ads');
          if($this->db->affected_rows() > 0){
          return true;
          } 
    }
}
?>