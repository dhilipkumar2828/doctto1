<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Doctors_model extends CI_Model {


    function __construct() {

        //load the parent constructor

        parent::__construct();
    }

    function get_doctors() 
    {
        $result = $this->db->order_by('id', 'DESC')->get('doctors')->result(); 
        
        if (count($result)>0) 
        {
            return $result;
        }
        else
        {
            return FALSE;
        }
    }
    
     function get_doc_bank_details() 
    {
        $result = $this->db->order_by('id', 'ASC')->get('doctor_bank_details')->result(); 
        
        if (count($result)>0) 
        {
            return $result;
        }
        else
        {
            return FALSE;
        }  
    }
    
    
     function get_specialisations() 
    {
        $result = $this->db->get('specialisation')->result();
        
        if (count($result)>0) 
        {
            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    function get_in_inactive_doctors(){
        $result = $this->db->where(array('doctor_show_status'=>'inactive'))->get('doctors')->result();
        if (count($result)>0) 
        {
            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    function get_in_active_doctors(){
        $result = $this->db->where(array('doctor_show_status'=>'active'))->get('doctors')->result();
        if (count($result)>0) 
        {
            return $result;
        }
        else
        {
            return FALSE;
        }
    }

        function insertData($hospital_name, $doctor_name, $hospital_image, $doctor_image, $designations, $youtube_channel_id, $password, $address,$email,$specialisation,$specialist,$license,$digital_signature, $mobile_number, $state, $city, $pincode, $experience, $consultant_fee_voice_call,$consultant_fee_video_call,$consultant_fee_chat, $aboutus, $tags, $doctor_show_status,$bluetick_status,$doctor_rating,$users_count, $doctor_login_status, $morning_start_time, $morning_end_time, $afternoon_start_time, $afternoon_end_time, $evening_start_time, $evening_end_time, $latitude, $longitude)
    {
        $array=array('hospital_name'=>$hospital_name,'doctor_name'=>$doctor_name,'hospital_image'=>$hospital_image,'doctor_image'=>$doctor_image,'designations'=>$designations,'youtube_channel_id'=>$youtube_channel_id,'password'=>$password,'address'=>$address,'email'=>$email,'specialisation'=>$specialisation,'specialist_in'=>$specialist,'doctor_license_no'=>$license,'digital_signature'=>$digital_signature,'mobile_number'=>$mobile_number,'state'=>$state,'city'=>$city,'pincode'=>$pincode,'experience'=>$experience,'voice_call'=>$consultant_fee_voice_call,'video_call'=>$consultant_fee_video_call,'chat_price'=>$consultant_fee_chat,'consultant_fee'=>$consultant_fee_chat,'aboutus'=>$aboutus,'tags'=>$tags,'doctor_show_status'=>$doctor_show_status,'blue_tick'=>$bluetick_status,'rating'=>$doctor_rating,'rating_count'=>$users_count,'doctor_login_status'=>$doctor_login_status,'morning_start_time'=>$morning_start_time,'morning_end_time'=>$morning_end_time,'morning_end_time'=>$morning_end_time,'afternoon_start_time'=>$afternoon_start_time,'afternoon_end_time'=>$afternoon_end_time,'evening_start_time'=>$evening_start_time,'evening_end_time'=>$evening_end_time, 'latitude' => $latitude, 'longitude' => $longitude);  
        
        
        $ins = $this->db->insert("doctors",$array);
        if($ins)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function add($data) {

        $this->db->set($data);
        $this->db->set('created_at', time());
        $this->db->insert('doctors');
       if ($this->db->insert_id()) {
       return $this->db->insert_id();
        }
    }

     function delete($parameters) {
         $this->db->where('id',$parameters['id']);
          $this->db->delete('doctors');
          if($this->db->affected_rows() > 0){
          return true;
          } 

}

    function update_record($table,$where,$data){
        $this->db->where($where);
        $this->db->set($data);
        $chk = $this->db->update($table);

        if($chk){
            return true;
        }
        else{
            return false;
        }
    }
}

?>