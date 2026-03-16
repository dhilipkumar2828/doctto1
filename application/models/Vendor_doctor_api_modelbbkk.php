<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vendor_doctor_api_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        //load database library
        $this->load->database();
    }

    function consults_for_today($date,$doctor_id){
        
        $today_count = $this->pending_appointment($date,$doctor_id);
        $completed = $this->completed($date,$doctor_id);
        $pending = $today_count-$completed;
        
        $ar = array('status' =>TRUE,'active_appointments'=>$today_count,'completed_appointments'=>$completed,'pending'=>$pending);
        return $ar;
    }
    function pending_appointment($date,$doctor_id){
        $table = "doctor_appointments";
        $this->db->select("id,date,doctor_status");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'active');
        return $this->db->get($table)->num_rows();
    }

    function completed($date,$doctor_id){
        $table = "doctor_appointments";
        $this->db->select("id,date,doctor_status");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'completed');
        return $this->db->get($table)->num_rows();
    }
    
    function get_designation_names_csv($designations_ids){
        $designations_ids_ar = explode(",", $designations_ids);
        
        $designations_array=[];

        foreach($designations_ids_ar as $d_id)
        {
            $this->db->select("name");
            $this->db->where("id",$d_id);
            $designations_array[]=$this->db->get("designations")->row()->name;
        }
        $designations_implode=implode(',', $designations_array);

        return $designations_implode;
    }    
    
    function day_schedules($doctor_id,$date){

        $table = "doctor_appointments";
        $this->db->select("id,doctor_id,doctor_status,date, time_slot_name, time_slot_value,consultation_fee,created_date");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->order_by("id","desc");
        $data = $this->db->get($table)->result();
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_status=$value->doctor_status;
                if($doctor_status=='active'){
                    $status="Waiting for Doctor Acceptancy";
                }
                elseif ($doctor_status=='accept') {
                    $status="Doctor Accepted you Booking";
                }
                elseif ($doctor_status=='completed') {
                    $status="completed";
                }
                elseif ($doctor_status=='reject') {
                    $status="Cancelled";
                }
                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $doctor_status=$value->doctor_status;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;

                if($value->time_slot_name=='morning'){
                    $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                    $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                    $time_slot_value=$time_slot_value.":00 PM";
                } 
                $table_doctor = "doctors";
                $data1 = $this->db->get($table_doctor)->row();
                $hospital_name = $data1->hospital_name;
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url()."uploads/shops/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('id'=>$value->id,'hospital_name'=>$hospital_name,'doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'date'=>$date,'time_slot_value'=>$time_slot_value,'created_date'=>$created_date,'consultation_fee'=>$consultation_fee,'doctor_status'=>$status); 
            }
            if(count($array)>0)
            {   
                $ar = array('status' =>TRUE,'data'=>$array);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar; 
            } 
        }
         else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }   
    }  

    function patient_management($doctor_id,$doctor_status){

        $table = "doctor_appointments";
        $this->db->select("id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,created_date");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",$doctor_status);
        $this->db->order_by("id","desc");
        $data = $this->db->get($table)->result();
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_status=$value->doctor_status;
                if ($doctor_status=='completed') {
                    $status="Appointment Completed";
                }
                elseif ($doctor_status=='reject') {
                    $status="Cancelled";
                }
                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                $table_doctor = "doctors";
                $data1 = $this->db->get($table_doctor)->row();
                $hospital_name = $data1->hospital_name;
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url()."uploads/shops/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('id'=>$value->id,'hospital_name'=>$hospital_name,'doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'date'=>$date,'time_slot_value'=>$time_slot_value,'created_date'=>$created_date,'consultation_fee'=>$consultation_fee,'doctor_status'=>$status); 
            }
            if(count($array)>0)
            {   
                $ar = array('status' =>TRUE,'data'=>$array);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar; 
            } 
        }
         else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }
    }  

     function waiting_accepting($doctor_id,$doctor_status){

        $table = "doctor_appointments";
        $this->db->select("id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,created_date");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",$doctor_status);
        $this->db->order_by("id","desc");
        $data = $this->db->get($table)->result();
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_status=$value->doctor_status;
               //echo $doctor_status; die;
                if ($doctor_status=='active') {
                    
                    $status="Need to Accepting";
                }
                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                $table_doctor = "doctors";
                $data1 = $this->db->get($table_doctor)->row();
                $hospital_name = $data1->hospital_name;
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url()."uploads/shops/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('id'=>$value->id,'hospital_name'=>$hospital_name,'doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'date'=>$date,'time_slot_value'=>$time_slot_value,'created_date'=>$created_date,'consultation_fee'=>$consultation_fee,'doctor_status'=>$status); 
            }
            if(count($array)>0)
            {   
                $ar = array('status' =>TRUE,'data'=>$array);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar; 
            } 
        }
         else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }
    }

    function my_dashboard($doctor_id){

        $table = "doctor_appointments";
        $this->db->select("id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",'completed');
        $this->db->order_by("id","desc");
        $data = $this->db->get($table)->result();
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;

                if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                
                $table_doctor = "doctors";
                $data1 = $this->db->get($table_doctor)->row();
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url()."uploads/shops/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'date'=>$date,'time_slot_value'=>$time_slot_value,'consultation_fee'=>$consultation_fee); 
            }
            if(count($array)>0)
            {   
                $ar = array('status' =>TRUE,'Appointment_history'=>$array);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar; 
            } 
        }
         else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }
    }  


    function appointment_single_schedule_details($doctor_id){

        $table = "doctor_appointments";
        $this->db->select("id,doctor_id,doctor_status,date,time_slot_name,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",'completed');
        $this->db->order_by("id","desc");
        $data = $this->db->get($table)->result();
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                $time_slot_name=$value->time_slot_name;
                $patient_name=$value->patient_name;
                $patient_mobile=$value->patient_mobile;
                $patient_age=$value->patient_age;
                $patient_visiting_purpose=$value->patient_visiting_purpose;
                if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                
                $table_doctor = "doctors";
                $data1 = $this->db->get($table_doctor)->row();
                $doctor_name = $data1->doctor_name;
                $hospital_name = $data1->hospital_name;
                $address = $data1->address;
                $doctor_image = base_url()."uploads/shops/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'hospital_name'=>$hospital_name,'address'=>$address,'date'=>$date,'time_slot_name'=>$time_slot_name,'time_slot_value'=>$time_slot_value,'patient_name'=>$patient_name,'patient_mobile'=>$patient_mobile,'patient_age'=>$patient_age,'patient_visiting_purpose'=>$patient_visiting_purpose,'consultation_fee'=>$consultation_fee); 
            }
            if(count($array)>0)
            {   
                $ar = array('status' =>TRUE,'data'=>$array);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar; 
            } 
        }
         else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }
    }  

    function notification_count($doctor_id){
        $table = "doctor_appointments";
        $this->db->select("id,doctor_status");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",'active');
        $data = $this->db->get($table)->num_rows();
        $ar = array('status' =>TRUE,'notification'=>$data);
        return $ar;
    }

    function appointment_cancel($patient_id,$appointment_id,$reason,$comments){

        $data =array('reason'=>$reason,'comments'=>$comments,'doctor_status'=>'reject');
        $where = array('id'=>$appointment_id);
        $table = "doctor_appointments";
        $res = $this->db->update($table,$data,$where);
        //echo $this->db->last_query(); die;
        if($res)
        {
            $arr = array('status'=>TRUE,'message'=>"Appointment cancelled successfully");
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Something went wrong");
            return $arr;  
        }
    }
}

?>
