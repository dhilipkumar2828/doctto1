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
    
    
    
            public function lab_tests_post() 
    {
            
              $doctor_id = $this->post('doctor_id');
              $patient_id = $this->post('patient_id');
              $appointment_id = $this->post('appointment_id');
              $prescription_type = $this->post('prescription_type');
            //   $prescription_or_lab_type = $this->post('prescription_or_lab_type');
              $lab_test_name = $this->post('lab_test_name');
              $lab_test_description = $this->post('lab_test_description');
            //   $lab_test_created_at = $this->post('lab_test_created_at');
             
               $chk = $this->vendor_doctor_api_model->labtests($doctor_id,$patient_id,$appointment_id,$prescription_type,$lab_test_name,$lab_test_description);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
      public function manual_prescription_post() 
    {
              $doctor_id = $this->post('doctor_id');
              $patient_id = $this->post('patient_id');
              $appointment_id = $this->post('appointment_id');
              $prescription_type = $this->post('prescription_type');
            //   $prescription_or_lab_type = $this->post('prescription_or_lab_type');
              $image = $this->post('image');
            
               $chk = $this->vendor_doctor_api_model->manual_prescription_update($doctor_id,$patient_id,$appointment_id,$prescription_type,$image);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
          public function diagnosis_post() 
    {
              $doctor_id = $this->post('doctor_id');
              $patient_id = $this->post('patient_id');
              $appointment_id = $this->post('appointment_id');
              $prescription_type = $this->post('prescription_type');
             
               $type = $this->post('type');
                $comment = $this->post('comment');
               $chk = $this->vendor_doctor_api_model->diagnosis_update($doctor_id,$patient_id,$appointment_id,$prescription_type,$type,$comment);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    function file_upload_post()
    {
            // $appointment_id =  $this->post('appointment_id');
             $folder_path =  $this->post('folder_path');
            $chk = $this->vendor_doctor_api_model->browse_file($folder_path);
            $this->response($chk, REST_Controller::HTTP_OK); 
    }
    
    
    
        public function doctor_registration_post() 
    {
              $doctor_name = $this->post('doctor_name');
              $mobile_number = $this->post('mobile_number');
               $email = $this->post('email');
           
              $password = md5($this->post('password')); 
            //   $re_enter_password = $this->post('re_enter_password');
              
            //   $platform = $this->post('platform');
            //   $device_name = $this->post('device_name');
              //$location = $this->post('location');,'home_location'=>$location,'lat'=>$latitude,'lng'=>$longitude
             // $latitude = $this->post('latitude');
              //$longitude = $this->post('longitude');
          
             
            $data = array('doctor_name' =>$doctor_name,'mobile_number'=>$mobile_number,'email'=>$email,'password' =>$password,); 
         
               $chk = $this->vendor_doctor_api_model->doRegister($data);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    
          public function update_token_post() 
    {
              $doctor_id= $this->post('doctor_id');
              $token = $this->post('token');
              $platform = $this->post('platform');

              $chk = $this->vendor_doctor_api_model->update_token($doctor_id,$token,$platform);
              if($chk=='error')
              {
                  $this->response($chk, REST_Controller::HTTP_OK);  
              }
              else
              {
                  $this->response($chk, REST_Controller::HTTP_OK);
              }
    }
    
    
    
         public function forgot_password_otp_post() 
    {
              $doctor_id = $this->post('doctor_id');
              $otp = $this->post('otp');
               $chk = $this->vendor_doctor_api_model->forgot_verify_OTP($doctor_id,$otp);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    
    
        public function otp_verification_post() 
    {
              $doctor_id = $this->post('doctor_id');
              $otp = $this->post('otp');
               $chk = $this->vendor_doctor_api_model->verify_OTP($doctor_id,$otp);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    
        public function resend_otp_post() 
    {
               $doctor_id = $this->post('doctor_id');
               $chk = $this->vendor_doctor_api_model->resendOTP($doctor_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }
    
    
        function resetPassword_post()
    {
              //$otp = $this->post('otp');
              $password = $this->post('password');
              $mobile_number = $this->post('mobile_number');
               $chk = $this->vendor_doctor_api_model->resetPassword($mobile_number,$password);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }

    
    
       public function forgotpassword_post()
    {
               $username = $this->post('username');
               $chk = $this->vendor_doctor_api_model->checkForgot($username);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
    }





               public function past_prescription_result_post() 
    {
            //   $doctor_id = $this->post('doctor_id');
            //   $patient_id = $this->post('patient_id');
                $appointment_id = $this->post('appointment_id');
                $prescription_id = $this->post('prescription_id');
       
               $chk = $this->vendor_doctor_api_model->past_prescription_result($prescription_id,$appointment_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
           public function past_prescription_count_post() 
    {
            //   $doctor_id = $this->post('doctor_id');
            //   $patient_id = $this->post('patient_id');
            $appointment_id = $this->post('appointment_id');
              $prescription_id = $this->post('prescription_id');
       
               $chk = $this->vendor_doctor_api_model->past_prescription_count($prescription_id,$appointment_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
           public function medication_post() 
    {
            //   $patient_prescription_id = $this->post('patient_prescription_id');
              $doctor_id = $this->post('doctor_id');
              $patient_id = $this->post('patient_id');
              $appointment_id = $this->post('appointment_id');
              $prescription_type = $this->post('prescription_type');
            //   $prescription_or_lab_type = $this->post('prescription_or_lab_type');
              $medication_name = $this->post('medication_name');
              $dosage = $this->post('dosage');
              $duration = $this->post('duration');
              $repeat = $this->post('repeat');
              $time_of_the_day = $this->post('time_of_the_day');
              $to_be_taken = $this->post('to_be_taken');
            //   $prescription_created_at = $this->post('prescription_created_at');
            
               $chk = $this->vendor_doctor_api_model->medication($doctor_id,$patient_id,$appointment_id,$prescription_type,$medication_name,$dosage,$duration,$repeat,$time_of_the_day,$to_be_taken);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
           public function medication_preview_post() 
    {
        
              $appointment_id = $this->post('appointment_id');
            //   $prescription_id = $this->post('prescription_id');
         
            
               $chk = $this->vendor_doctor_api_model->medication_preview($appointment_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
      
           public function get_lab_tests_preview_post() 
    {
        
              $appointment_id = $this->post('appointment_id');
            //   $prescription_id = $this->post('prescription_id');
         
            
               $chk = $this->vendor_doctor_api_model->labtests_preview($appointment_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
               public function medication_preview_update_post() 
    {
              $id = $this->post('id');
            //   $doctor_id = $this->post('doctor_id');
            //   $patient_id = $this->post('patient_id');
            //   $appointment_id = $this->post('appointment_id');
            //   $prescription_id = $this->post('prescription_id');
              $medication_name = $this->post('medication_name');
              $dosage = $this->post('dosage');
              $duration = $this->post('duration');
              $repeat = $this->post('repeat');
              $time_of_the_day = $this->post('time_of_the_day');
              $to_be_taken = $this->post('to_be_taken');
            //   $prescription_created_at = $this->post('prescription_created_at');
            
               $chk = $this->vendor_doctor_api_model->medication_preview_update($id,$medication_name,$dosage,$duration,$repeat,$time_of_the_day,$to_be_taken);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
                 public function lab_tests_update_post() 
    {
              $id = $this->post('id');
              $lab_test_name = $this->post('lab_test_name');
              $lab_test_description = $this->post('lab_test_description');
       
            
               $chk = $this->vendor_doctor_api_model->labtests_update($id,$lab_test_name,$lab_test_description);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
                public function medication_preview_delete_post() 
    {
               $id = $this->post('id');
               
               $chk = $this->vendor_doctor_api_model->medication_preview_delete($id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
                public function labtests_delete_post() 
    {
               $id = $this->post('id');
               
               $chk = $this->vendor_doctor_api_model->lab_tests_delete($id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
         
        public function prescription_single_post() 
        
    {         $id = $this->post('id');
              $doctor_id = $this->post('doctor_id');
              $patient_id = $this->post('patient_id');
              $appointment_id = $this->post('appointment_id');
              $prescription_id = $this->post('prescription_id');
              $chief_complaints = $this->post('chief_complaints');
              $diagnosis = $this->post('diagnosis');
              $advice = $this->post('advice');
              $investigation = $this->post('investigation');
              $follow_up = $this->post('follow_up');
              $handwritten_prescription = $this->post('handwritten_prescription');
              $prescription_created_at = $this->post('prescription_created_at');
            
               $chk = $this->vendor_doctor_api_model->prescription_single($id,$doctor_id,$patient_id,$appointment_id,$prescription_id,$chief_complaints,$diagnosis,$advice,$investigation,$follow_up,$handwritten_prescription,$prescription_created_at);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
       public function prescription_status_check_post() 
        
    {
              $appointment_id = $this->post('appointment_id');
               $chk = $this->vendor_doctor_api_model->prescription_status_check($appointment_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
   
     
        public function prescription_post() 
    {
            //   $prescription_id = $this->post('prescription_id');
       
               $chk = $this->vendor_doctor_api_model->prescription();
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
        public function doctor_location_post() 
    {
               $doctor_id = $this->post('doctor_id');
               $latitude = $this->post('latitude');
               $longitude = $this->post('longitude');
               $chk = $this->vendor_doctor_api_model->doctorLocation($doctor_id,$latitude,$longitude);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
    
    
    public function update_doctor_bank_details_post()
    {
          
        $doctor_id = $this->post('doctor_id');
        $bank_name = $this->post('bank_name');
        $account_holder_name = $this->post('account_holder_name');
        $account_number = $this->post('account_number');
        $retype_account_number = $this->post('retype_account_number'); 
        $ifsc_code = $this->post('ifsc_code');
        // $status = $this->post('status');  
        
      
        
        $chk = $this->vendor_doctor_api_model->update_bankdetails($doctor_id,$bank_name,$account_holder_name,$account_number,$retype_account_number,$ifsc_code);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }


    public function get_doctor_bank_details_post()
    {
          
        $doctor_id = $this->post('doctor_id');
    
     
        $chk = $this->vendor_doctor_api_model->get_bankdetails($doctor_id);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }


  public function get_timeslots_post()
    {
          
        $id = $this->post('id');
    
     
        $chk = $this->vendor_doctor_api_model->get_slots($id);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }
    
    
      public function update_time_slots_post()
    {
          
        $id = $this->post('id');
        $morning_start_time = $this->post('morning_start_time');
        $morning_end_time = $this->post('morning_end_time');
        $afternoon_start_time = $this->post('afternoon_start_time');
        $afternoon_end_time = $this->post('afternoon_end_time'); 
        $evening_start_time = $this->post('evening_start_time');
        $evening_end_time = $this->post('evening_end_time');  
        
      
        
        $chk = $this->vendor_doctor_api_model->update_slots($id,$morning_start_time,$morning_end_time,$afternoon_start_time,$afternoon_end_time,$evening_start_time,$evening_end_time);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }

      public function delete_doctor_bank_details_post()
    {
          
        $doctor_id = $this->post('doctor_id');
    
     
        $chk = $this->vendor_doctor_api_model->delete_bankdetails($doctor_id);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        } else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }
    
    
    
    
    
    
    

    public function login_post() 
    {
            //   $pincode = $this->post('pincode');
              $username = $this->post('username');
              $password = md5($this->post('password'));
              $token = $this->post('token');
               $chk = $this->vendor_doctor_api_model->checkLogin($username,$password,$token);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       
    }
      
    
    public function consults_for_day_post() 
    {
    
      $doctor_id = $this->input->post('doctor_id');
      $sql = $this->vendor_doctor_api_model->consults_for_today($doctor_id);
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
      $doctor_id = $this->input->post('doctor_id');
      $selected_date  = $this->input->post('selected_date');
      $sql = $this->vendor_doctor_api_model->day_schedules($doctor_id,$selected_date);

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

   public function appointment_details_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
      $appointment_id = $this->input->post('appointment_id');
      
      $sql = $this->vendor_doctor_api_model->appointmentDetails($doctor_id,$appointment_id); 

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }
   
   
      public function waiting_single_appointment_details_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->waitingAppointmentDetails($doctor_id,$appointment_id);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }

   public function appointment_completed_appointments_post() 
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

    public function earnings_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
       $start_date = $this->input->post('start_date');
       $end_date = $this->input->post('end_date');

       $filter_status = $this->input->post('filter_status');
      $sql = $this->vendor_doctor_api_model->earnings($doctor_id,$start_date,$end_date,$filter_status);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }


//   public function my_dashboard_post() 
//   {
      
//       $doctor_id = $this->input->post('doctor_id');
//       $start_date = $this->input->post('start_date');
//       $end_date = $this->input->post('end_date');
//       $sql = $this->vendor_doctor_api_model->doctorDashbord($doctor_id,$start_date,$end_date);

//       if($sql)
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);  
//       }
//       else
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);
//       }
       
//   }
   public function login_verification_post() 
   {
      
      $doctor_id = $this->input->post('doctor_id');
      $type = $this->input->post('type');
      $sql = $this->vendor_doctor_api_model->login_verification($doctor_id,$type);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
   }

   

//   public function notification_count_post() 
//     {
//       $doctor_id = $this->input->post('doctor_id');
//       $sql = $this->vendor_doctor_api_model->notification_count($doctor_id);
//       if($sql)
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);  
//       }
//       else
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);
//       }
       
//     }
//       public function pending_acceptance_count_post() 
//     {
//       $doctor_id = $this->input->post('doctor_id');
//       $sql = $this->vendor_doctor_api_model->pending_count($doctor_id);
//       if($sql)
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);  
//       }
//       else
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);
//       }
       
//     }
    
//       public function upcoming_schedules_count_post() 
//     {
//       $doctor_id = $this->input->post('doctor_id');
//       $sql = $this->vendor_doctor_api_model->accept_count($doctor_id);
//       if($sql)
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);  
//       }
//       else
//       {
//          $this->response($sql, REST_Controller::HTTP_OK);
//       }
       
//     }
    
        public function priscription_count_post() 
    {
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->eprescription_count($appointment_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    
    //      public function labtests_count_post() 
    // {
    //   $appointment_id = $this->input->post('appointment_id');
    //   $sql = $this->vendor_doctor_api_model->labtests_count($appointment_id);
    //   if($sql)
    //   {
    //      $this->response($sql, REST_Controller::HTTP_OK);  
    //   }
    //   else
    //   {
    //      $this->response($sql, REST_Controller::HTTP_OK);
    //   }
       
    // }
    
    //     public function manual_presription_count_post() 
    // {
    //   $appointment_id = $this->input->post('appointment_id');
    //   $sql = $this->vendor_doctor_api_model->manual_count($appointment_id);
    //   if($sql)
    //   {
    //      $this->response($sql, REST_Controller::HTTP_OK);  
    //   }
    //   else
    //   {
    //      $this->response($sql, REST_Controller::HTTP_OK);
    //   }
       
    // }
    
    
  public function prescription_count_post() 
    {
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->prescription_count($appointment_id);
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


    public function appointment_complete_post() 
    {
      $patient_id = $this->input->post('patient_id');
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->appointmentComplete($patient_id,$appointment_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

    public function accept_appointment_post() 
    {
      $doctor_id = $this->input->post('doctor_id');
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->acceptAppointment($doctor_id,$appointment_id);

      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    
    public function update_doctor_details_post() 
    {

        $doctor_id = $this->input->post('doctor_id');
        $doctor_name = $this->input->post('doctor_name');
        $doctor_image = $this->input->post('doctor_image');
        $cover_image = $this->input->post('cover_image');
        $digital_signature = $this->input->post('digital_signature');
        $designations = $this->input->post('designation');
        $experience = $this->input->post('experience');
        $mobile_number = $this->input->post('mobile_number');
        $aboutus = $this->input->post('aboutus');
        $gender = $this->input->post('gender'); 
        $doctor_license_no = $this->input->post('doctor_license_no');
        
        $voice_call = $this->input->post('voice_call'); 
        $video_call = $this->input->post('video_call'); 
        $morning_start_time = $this->input->post('morning_start_time');
        $morning_end_time = $this->input->post('morning_end_time');
        $afternoon_start_time = $this->input->post('afternoon_start_time');
        $afternoon_end_time = $this->input->post('afternoon_end_time');
        $evening_start_time = $this->input->post('evening_start_time');
        $evening_end_time = $this->input->post('evening_end_time');
        $bank_name = $this->input->post('bank_name');
        $account_holder_name = $this->input->post('account_holder_name');
        $account_number = $this->input->post('account_number');
        $retype_account_number = $this->input->post('retype_account_number');
        $ifsc_code = $this->input->post('ifsc_code');
        $chat_price = $this->input->post('chat_price'); 

        
         
        $specialisation= $this->input->post('specialisation'); 
        $specialist_in= $this->input->post('specialist_in'); 
        
        $sql = $this->vendor_doctor_api_model->update_doctor_details($doctor_id,$doctor_name,$doctor_image,$cover_image,$digital_signature,$designations,$experience,$mobile_number,$aboutus,$gender,$doctor_license_no,$voice_call,$video_call,$morning_start_time,$morning_end_time,$afternoon_start_time,$afternoon_end_time,$evening_start_time,$evening_end_time,$bank_name,$account_holder_name,$account_number,$retype_account_number,$ifsc_code,$chat_price,$specialisation,$specialist_in);

            if($sql)
            {
               $this->response($sql, REST_Controller::HTTP_OK);  
            }
            else
            {
               $this->response($sql, REST_Controller::HTTP_OK);
            }
    }
    
    
        public function check_kyc_details_post() 
    {

        $doctor_id = $this->input->post('doctor_id');
   
        $sql = $this->vendor_doctor_api_model->check_kyc_details($doctor_id);

            if($sql)
            {
               $this->response($sql, REST_Controller::HTTP_OK);  
            }
            else
            {
               $this->response($sql, REST_Controller::HTTP_OK);
            }
    }
    
    public function doctor_status_post() 
    {
      $doctor_id = $this->input->post('doctor_id');
      $doctor_show_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->doctor_status($doctor_id,$doctor_show_status);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    
       public function get_doctor_status_post() 
    {
      $doctor_id = $this->input->post('doctor_id');
      $doctor_show_status = $this->input->post('doctor_status');
      $sql = $this->vendor_doctor_api_model->get_doctor_status($doctor_id,$doctor_show_status);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    
          public function get_manual_prescription_post() 
    {
      $appointment_id = $this->input->post('appointment_id');
      $sql = $this->vendor_doctor_api_model->get_manual_prescription($appointment_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }
    
      public function upload_images_post() 
    {
      $sql = $this->vendor_doctor_api_model->upload_images();
      
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
   }

    public function upload_hospital_images_post() 
    {
      $sql = $this->vendor_doctor_api_model->uploadHospitalImages();
      
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
   }
   
    public function doctors_details_post() 
    { 
      $doctor_id = $this->post('doctor_id');
      $sql = $this->vendor_doctor_api_model->doctors_details($doctor_id);
      
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

     public function get_designation_post() 
    { 
      $sql = $this->vendor_doctor_api_model->getDesignation();
      
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }

    public function get_specialisation_post() 
    { 
      $sql = $this->vendor_doctor_api_model->getSpecialisation();
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }


    
    public function get_specialist_in_post() 
    { 
    	$specialisation_id = $this->post('specialisation_id');
      $sql = $this->vendor_doctor_api_model->getSpecialistIn($specialisation_id);
      if($sql)
      {
         $this->response($sql, REST_Controller::HTTP_OK);  
      }
      else
      {
         $this->response($sql, REST_Controller::HTTP_OK);
      }
       
    }


    function version_control_post()
    {
         $chk = $this->vendor_doctor_api_model->versionControl();
         $this->response($chk, REST_Controller::HTTP_OK);     
    }

    

}



?>