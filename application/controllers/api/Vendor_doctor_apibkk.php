<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Vendor_doctor_api extends REST_Controller {

    public function __construct() 
    { 
      header('Access-Control-Allow-Origin: *');
      header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        //load user model
        $this->load->model('vendor_doctor_api_model');
        //$this->load->library('email'); 
    }

    
    public function consults_for_day_post() 
    {
      $date = date('Y-m-d');
      $doctor_id = $this->input->post('doctor_id');
      $sql = $this->vendor_doctor_api_model->consults_for_today($date,$doctor_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

   public function appointment_schedules_post() 
    {
      $date = date('Y-m-d');
      $doctor_id = $this->input->post('doctor_id');
      $sql = $this->vendor_doctor_api_model->day_schedules($doctor_id,$date);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    

   public function patient_management_post() 
    {
      
      $doctor_id = $this->input->post('doctor_id');
      $doctor_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->patient_management($doctor_id,$doctor_status);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

   public function waiting_accepting_post() 
    {
      
      $doctor_id = $this->input->post('doctor_id');
      $doctor_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->waiting_accepting($doctor_id,$doctor_status);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }


   public function my_dashboard_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
      //$doctor_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->my_dashboard($doctor_id);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }

   public function appointment_single_schedule_details_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
      //$doctor_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->appointment_single_schedule_details($doctor_id);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }

   public function notification_count_post() 
    {
      $doctor_id = $this->input->post('doctor_id');
      $sql = $this->vendor_doctor_api_model->notification_count($doctor_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

   public function cancel_appointment_post() 
    {
      $patient_id = $this->input->post('patient_id');
      $appointment_id = $this->input->post('appointment_id');
      $reason = $this->input->post('reason');
      $comments = $this->input->post('comments');
      $sql = $this->vendor_doctor_api_model->appointment_cancel($patient_id,$appointment_id,$reason,$comments);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

}



?>