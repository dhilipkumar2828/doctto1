
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vendor_doctor_api_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        //load database library
        $this->load->database();
    }
    
    
        function manual_prescription_update($doctor_id,$patient_id,$appointment_id,$prescription_type,$image)
    {
                   $this->db->where("appointment_id",$appointment_id);
        $chk_row = $this->db->get("patient_prescription")->row();
        if(!empty($chk_row))
        {
            //  $pres_id =$chk_row->id; 
              
              $data = array('manual_prescription'=>$image);
              $wr =   array('appointment_id'=>$appointment_id);
              $upd = $this->db->update("patient_prescription",$data,$wr);
              if($upd){
                  
                     return array('status'=>TRUE,'message'=>"Updated Successfully");
              }
        }
        else
        {
         
            $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'manual_prescription'=>$image,'created_at'=>time());
            $ins = $this->db->insert("patient_prescription",$array); 
           if($ins)
           {
              return array('status'=>TRUE,'message'=>"Inserted Successfully");
                    
           }
           
        }
    
   
     }
     
             function diagnosis_update($doctor_id,$patient_id,$appointment_id,$prescription_type,$type,$comment)
    {
                   $this->db->where("appointment_id",$appointment_id);
        $chk_row = $this->db->get("patient_prescription")->row();
        
    
        if(!empty($chk_row))
        {
            
            //  $pres_id =$chk_row->id; 
              if($type=='chief_complaints')
              {
                  $data = array('chief_complaints'=>$comment,'prescription_type'=>$prescription_type);
              }
              else if($type=='diagnosis')
              {
                 
                  $data = array('diagnosis'=>$comment,'prescription_type'=>$prescription_type);
              }
                else if($type=='advice')
              {
                  $data = array('advice'=>$comment,'prescription_type'=>$prescription_type);
              }
                else if($type=='investigation')
              {
                  $data = array('investigation'=>$comment,'prescription_type'=>$prescription_type);
              }
                else if($type=='followup')
              {
                  $data = array('followup'=>$comment,'prescription_type'=>$prescription_type);
              }
              
              $wr =   array('appointment_id'=>$appointment_id);
              $upd = $this->db->update("patient_prescription",$data,$wr);
            //   echo $this->db->last_query();die;
              if($upd){
                  
                     return array('status'=>TRUE,'message'=>"Updated Successfully");
              }
        }
        else
        {
             if($type=='chief_complaints')
              {
                   $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'chief_complaints'=>$comment,'created_at'=>time());
              }
              else if($type=='diagnosis')
              {
                   $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'diagnosis'=>$comment,'created_at'=>time());
              }
              
                else if($type=='advice')
              {
                   $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'advice'=>$comment,'created_at'=>time());
              }
              
                else if($type=='investigation')
              {
                   $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'investigation'=>$comment,'created_at'=>time());
              }
              
                else if($type=='followup')
              {
                   $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'followup'=>$comment,'created_at'=>time());
              }
           
            $ins = $this->db->insert("patient_prescription",$array); 
           if($ins)
           {
              return array('status'=>TRUE,'message'=>"Inserted Successfully");
                    
           }
           
        }
    
   
     }
    
    
    function browse_file($folder_path)
{
    // $this->db->select('*');
//                                  $this->db->where('id',$user_id);
//     $check_image_exists_or_not = $this->db->get('users')->row();
    
    
    $image = $this->upload_image('image',$folder_path);
    //$upd =$this->db->update('patient_prescription',array('manual_prescription'=>$image),array('id'=>$appointment_id));
    

    $image_full_path=base_url()."uploads/".$folder_path."/".$image;
         return array('status' =>TRUE,'image'=>$image,'image_full_path'=>$image_full_path);
    
      
}




    private function upload_image($file_name,$folder_path) {
        /*if($_FILES[$file_name]["size"]<'5114374')
        {*/
            //echo $_FILES[$file_name]["image"]; die;
            
            if($folder_path == "prescription"){
                 $upload_path1 = "./uploads/prescription/";
            }
            else if($folder_path == "doctors"){
                 $upload_path1 = "./uploads/doctors/";
            }
           
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;

            return $fileDetailArray1['file_name'];
      /* }
        else
        {
            return 'false';
        }*/
        
    }
    
    function doRegister($data)
    {
        
     
        $mobile_number = $data['mobile_number'];
        $email = $data['email'];
        $otp = RAND;
        //$otp = '1234';
       
        $otp_message = $otp." is OTP to register with Doctto. Pls do not share OTP to anyone for security reasons. Thanks & Regards...! DOCTTO";
        $template_id = '1407168691870730340';

        $data['otp'] = $otp;
        $phone_verify = $this->db->query("select * from doctors where mobile_number='".$mobile_number."' and otp_status=1");
        $email_verify = $this->db->query("select * from doctors where email='".$email."' and otp_status=1");
        
        //                 $this->db->where('mobile_number',$mobile_number);
        //                 $this->db->where('otp_status','1');
        // $phone_verify = $this->db->get('doctors');
        
        
        //                 $this->db->where('email',$email);
        //                 $this->db->where('otp_status','1');
        // $email_verify = $this->db->get('doctors');
        
        // print_r($email_verify);die;
        // echo $this->db->last_query();die;
       
        if($phone_verify->num_rows()>0)
        {
    
            return array('status' =>FALSE, 'message'=>"Phone Number already Exist ");
        }
        
            
        if($email_verify->num_rows()>0){
            
                 return array('status' =>FALSE, 'message'=>"Email already Exist ");
             }
        else
        {
            // $chk_both = $this->db->query("select * from doctors where mobile_number='".$mobile_number."' and otp_status=0");
            
                        $this->db->select('*');
                        $this->db->where('mobile_number',$mobile_number);
                        $this->db->where('otp_status','0');
          $chk_both  =  $this->db->get('doctors');    
                        
            if($chk_both->num_rows()>0)
            {
                $get = $chk_both->row();
                $wr = array('mobile_number'=>$mobile_number);
                
                
            $this->user->send_message($otp_message,$mobile_number,$template_id);


                $this->sendmail($email,$otp_message);
                

                    $ins = $this->db->update("doctors",$data,$wr);
                    $last_id = $get->id;
                    if($ins)
                    {
                        $to_mail = $email;
                        $ar=array('status' =>TRUE,'doctor_id'=>(int)$last_id,'otp'=>$otp,'mobile_number'=>$mobile_number,'message'=>"Please enter your OTP");
                        return $ar;
                    }
            }
            else
            {
                $get = $chk_both->row();
                
                 $this->user->send_message($otp_message,$mobile_number,$template_id);
                $this->sendmail($email,$otp_message);

                    $ins = $this->db->insert("doctors",$data);
                    $last_id = $this->db->insert_id($ins);
                    if($ins)
                    {
                        $to_mail = $email;

                        $ar=array('status' =>TRUE,'doctor_id'=>$last_id,'otp'=>$otp,'mobile_number'=>$mobile_number,'message'=>"Please enter your OTP");
                        return $ar;
                    }
            
            }
        }
    }
    
    
          function update_token($doctor_id,$token,$platform)
    {
        
                        $this->db->where('id',$doctor_id);
          $chk_both  =  $this->db->get('doctors')->num_rows();    

          if($chk_both>0)             
             {
                $upd = array('token'=>$token,'platform'=>$platform);
                $wr = array('id'=>$doctor_id); 
                 $ins = $this->db->update("doctors",$upd,$wr);
                    if($ins)
                    {
                        $ar=array('status' =>TRUE,'message'=>"Data Updated Successfully");
                        return $ar;
                    }
             }
            else
            {
                        $ar=array('status' =>FALSE,'message'=>"Invalid Id");
                        return $ar;
            
            }
        
    }
    
    
          function forgot_verify_OTP($doctor_id,$otp)
    {
         $qry = $this->db->query("select * from doctors where id='".$doctor_id."' and otp='".$otp."'");
         if($qry->num_rows()>0)
         {
            $ar=array('otp_status'=>1);
            $wr=array('id'=>$doctor_id);
            $ins = $this->db->update("doctors",$ar,$wr);
            if($ins)
            {

                    $stu_row = $qry->row();
                    $mobile_number =$stu_row->mobile_number; 
                   $otp_message = "Dear ".$stu_row->doctor_name." your successfully registered with Doctto. Enjoy your local shopping experience with A3 Services. ";
                   $template_id ="1407161683204290058";
            /*if($this->send_message($otp_message,$phone,$template_id))
            {*/

                    //$st_email = $stu_row->email;
                //$to_mail = $st_email;
            //}

              $row = $qry->row();
              $name = $row->doctor_name; 
              $res = array('status' =>TRUE,'doctor_id'=>$row->id,'name'=>$name,'mobile_number'=>$row->mobile_number,'message'=>"Otp verified , Reset your password");
              return $res;  
            }
         }   
         else
         {
             return array('status' =>FALSE, 'message'=>"Invalid OTP");
         }  
    }
    
        function verify_OTP($doctor_id,$otp)
    {
         $qry = $this->db->query("select * from doctors where id='".$doctor_id."' and otp='".$otp."'");
         if($qry->num_rows()>0)
         {
            $ar=array('otp_status'=>1);
            $wr=array('id'=>$doctor_id);
            $ins = $this->db->update("doctors",$ar,$wr);
            if($ins)
            {
                    $stu_row = $qry->row();
                    $email = $stu_row->email;
                    // print_r($email);die;
                    // print_r($stu_row);die;
                    $mobile_number =$stu_row->mobile_number; 
                   $otp_message = "Dear ".$stu_row->doctor_name." your successfully registered with Doctto.";
                   $template_id ="1407161683204290058";
                   
                   
                    $this->sendmail($email,$otp_message);
                   
            /*if($this->send_message($otp_message,$phone,$template_id))
            {*/

                    //$st_email = $stu_row->email;
                //$to_mail = $st_email;
            //}

              $row = $qry->row();
              $name = $row->doctor_name; 
              $res = array('status' =>TRUE,'doctor_id'=>$row->id,'name'=>$name,'mobile_number'=>$row->mobile_number,'message'=>"Registration Success,An confirmation email has been sent to your email");
              return $res;  
            }
         }   
         else
         {  
             return array('status' =>FALSE, 'message'=>"Invalid OTP");
         }  
    }
    
        function resendOTP($doctor_id)
    {
        $chk = $this->db->query("select * from doctors where id='".$doctor_id."'");
        if($chk->num_rows()>0)
        {
            $row = $chk->row();
            $otp = RAND; 
            //rand(1000,10000);
            //$otp='1234';
            $phone = $row->mobile_number;
            $email = $row->email;

            
              $otp_message = $otp." is OTP to register with Doctto. Pls do not share OTP to anyone for security reasons. Thanks & Regards...! DOCTTO";
            $template_id = '1407168691870730340';

            $this->user->send_message($otp_message,$phone,$template_id);


            $this->sendmail($email,$otp_message);
            
                $ar = array('otp'=>$otp);
                $wr = array('id'=>$doctor_id);
                $upd = $this->db->update("doctors",$ar,$wr);
                if($upd)
                {
                    $ar = array('status' =>TRUE,'message'=>"OTP sent to your Mobile Number And Email");
                    return $ar;
                }
        }
        else
        {
            $ar = array('status' =>FALSE,'message'=>"Invalid Doctor ID");
            return $ar;
        }
    }

    
     function resetPassword($mobile_number,$password)
    {
        // and otp='".$otp."'
        $qry = $this->db->query("select * from doctors where mobile_number='".$mobile_number."'");
        if($qry->num_rows()>0)
        {
              $ar = array('password'=>md5($password));
              $wr = array('mobile_number'=>$mobile_number);
              $upd = $this->db->update("doctors",$ar,$wr);
            //   echo $this->db->last_query();die;
              if($upd)
              {
                  $row = $qry->row();
                  
              
                  $res = array('status' =>TRUE,'doctor_id'=>$row->id,'mobile_number'=>$row->mobile_number,'name'=>$row->doctor_name,'message'=>"Password Reset Successful");
                  return $res;
              }
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Invalid Mobile");
        }
    }
    
    
 function checkForgot($username)
    {


        // $chk = $this->db->query("select * from doctors where mobile_number='".$mobile_number."' and otp_status=1");
        
                  $this->db->where('mobile_number',$username);
                  $this->db->or_where('email',$username);
                  $this->db->where('otp_status','1');
           $chk = $this->db->get('doctors');
           
        //   echo $this->db->last_query();die;
        
        if($chk->num_rows()>0)
        {
            // print_R('asd');die;
            $otp = RAND;
            //rand(1000,10000);
            //$otp = '1234';
            $chk_row = $chk->row();
            
            
            $email = $chk_row->email;
            
            $ar=array('otp'=>$otp);
            $wr=array('mobile_number'=>$chk_row->mobile_number);
           


            $otp_message = $otp." is OTP to reset your password. Pls do not share OTP to anyone for security reasons. Thanks & Regards...! DOCTTO";
            $template_id = "1407168691874226229";
            $this->user->send_message($otp_message,$chk_row->mobile_number,$template_id);

            
         
            $mail=$this->sendmail($email,$otp_message);
          
           
            
                $upd = $this->db->update('doctors',$ar,$wr);
                if($upd)
                {
                    $res = array('status' =>TRUE,'doctor_id'=>$chk_row->id,'otp'=>$otp,'mobile'=>$chk_row->mobile_number,'email'=>$chk_row->email);
                    return $res;  
                }
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Invalid Phone Number or Email");
        }
    }
    
    
    function sendmail($email, $otp_message) {
     

        $from = "doctto@gmail.com";

        $data['message'] = $otp_message;
        $this->load->library('email');
        $config['protocol'] = 'smtp';

       
        $config['smtp_host'] = 'smtp-relay.sendinblue.com';
        $config['smtp_user'] = 'doctto108@gmail.com';
        $config['smtp_pass'] = '1IFhV9fJy5DnWY6w';

//                $config['smtp_host'] = 'smtp-relay.sendinblue.com';
//            $config['smtp_user'] = 'signetmail@gmail.com';
//            $config['smtp_pass'] = 'EnBh5VpQbGcOKzkg';

        $config['smtp_port'] = '587';

        $config['smtp_timeout'] = '7';

        $config['charset'] = 'utf-8';

        $config['newline'] = "\r\n";

        $config['mailtype'] = 'html'; // or text

        $config['validation'] = FALSE;

        $this->email->initialize($config);

        $this->email->set_newline("\r\n");

        $this->email->from($from);

        $this->email->to($email);

        $this->email->subject('mail success');

        // $this->email->message($this->load->view('replytemplate.php', $data, true));

        $this->email->message($otp_message);

        $send = $this->email->send();

        if ($send) {

//                    echo "test"; die;
//
//                    echo json_encode($send);

            return TRUE;
        } else {

            //echo "Not sent";

            $error = $this->email->print_debugger(array('headers'));

            //echo json_encode($error); die;

            return false;
 }
}
    
      function medication_preview_delete($id)
    {
   
                $this->db->where('id',$id);
       $res =   $this->db->count_all_results('eprescription');
            if($res)
            {
                               $this->db->where('id',$id);
                   $delete =   $this->db->delete('eprescription');
                
                     $report=array('status'=>TRUE,'message'=>"Medication Deleted Successfully");
                     return $report;
            }
               else 
            {
                     $report=array('status'=>FALSE,'message'=>"No Data Found");
                     return $report;
            }
            
     }
     
     
     
           function lab_tests_delete($id)
    {
   
                $this->db->where('id',$id);
       $res =   $this->db->count_all_results('lab_tests');
            if($res)
            {
                               $this->db->where('id',$id);
                   $delete =   $this->db->delete('lab_tests');
                
                     $report=array('status'=>TRUE,'message'=>"Row Deleted Successfully");
                     return $report;
            }
               else 
            {
                     $report=array('status'=>FALSE,'message'=>"Invalid Id");
                     return $report;
            }
            
     }

    function medication_preview_update($id,$medication_name,$dosage,$duration,$repeat,$time_of_the_day,$to_be_taken)
    {
                $this->db->where('id',$id);
                // $this->db->where('doctor_id',$doctor_id);
                // $this->db->where('patient_id',$patient_id);
                // $this->db->where('prescription_id',$prescription_id);
                // $this->db->where('appointment_id',$appointment_id);
                // $this->db->where('prescription_created_at',$prescription_created_at);
                
        $res =  $this->db->count_all_results('eprescription');
        if($res>0)
        {
          
                
                 $upd_data =array('medication_name'=>$medication_name,'dosage'=>$dosage,'duration'=>$duration,'repeat'=>$repeat,'time_of_the_day'=>$time_of_the_day,'to_be_taken'=>$to_be_taken,'prescription_created_at'=>time());
                     $wr =array('id'=>$id);
                 $upd = $this->db->update("eprescription",$upd_data,$wr); 
              
            //   echo $this->db->last_query();die;
           
              
        if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                     return $report;
                }
        }
        else{
               $report=array('status'=>FALSE,'message'=>"Invalid Id");
                     return $report;
        }
        
        
   
     }
     
       function labtests_update($id,$lab_test_name,$lab_test_description)
    {
                $this->db->where('id',$id);
        $res =  $this->db->count_all_results('lab_tests');
        if($res>0)
        {
                 $upd_data =array('lab_test_name'=>$lab_test_name,'lab_test_description'=>$lab_test_description,'lab_test_created_at'=>time());
                     $wr =array('id'=>$id);
                 $upd = $this->db->update("lab_tests",$upd_data,$wr); 
              
            //   echo $this->db->last_query();die;
           
              
        if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                     return $report;
                }
        }
        else{
               $report=array('status'=>FALSE,'message'=>"Invalid Id");
                     return $report;
        }
        
        
   
     }

    function medication_preview($appointment_id)
    {
               $this->db->select('id');
               $this->db->where('appointment_id',$appointment_id);
              // $this->db->where('prescription_type','prescription');
               $res = $this->db->get('patient_prescription')->row();
               
            //   echo $this->db->last_query();die;
        
            if($res)
            {
                     $pres_id = $res->id;
                     $this->db->where('patient_prescription_id',$pres_id);
             $data = $this->db->get('eprescription')->result();
                
          foreach($data as $value){
              $value->time_of_the_day = explode(',',$value->time_of_the_day);
          }
                
                     $report=array('status'=>TRUE,'data'=>$data);
                     return $report;
            }
               else 
            {
                     $report=array('status'=>FALSE,'message'=>"No Data Found");
                     return $report;
            }
            
     }
     
         function labtests_preview($appointment_id)
    {
   
               $this->db->select('id');
               $this->db->where('appointment_id',$appointment_id);
               //$this->db->where('prescription_type','prescription');
               $res = $this->db->get('patient_prescription')->row();
               
            //   echo $this->db->last_query();die;
            if(!empty($res))
            {
                     $pres_id = $res->id;
                     $this->db->where('patient_prescription_id',$pres_id);
             $data = $this->db->get('lab_tests')->result();
                    if(!empty($data))
                    {
                      $report=array('status'=>TRUE,'data'=>$data);
                     return $report;
                    }
                    else
                    {
                       $report=array('status'=>FALSE,'message'=>"No Data Found");
                     return $report;
                    }
                     
            }
               else 
            {
                     $report=array('status'=>FALSE,'message'=>"No Data Found");
                     return $report;
            }
            
            
     }
    function medication($doctor_id,$patient_id,$appointment_id,$prescription_type,$medication_name,$dosage,$duration,$repeat,$time_of_the_day,$to_be_taken)
    {
        
                   $this->db->where("appointment_id",$appointment_id);
        $chk_row = $this->db->get("patient_prescription")->row();
        if(!empty($chk_row))
        {
             $pres_id =$chk_row->id;   
        }
        else
        {
         
            $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'created_at'=>time());
            $this->db->insert("patient_prescription",$array);
            // 
            $pres_id =$this->db->insert_id();
            
           
        }
    
                 $ins_data =array('patient_prescription_id'=>$pres_id,'medication_name'=>$medication_name,'dosage'=>$dosage,'duration'=>$duration,'repeat'=>$repeat,'time_of_the_day'=>$time_of_the_day,'to_be_taken'=>$to_be_taken,'prescription_created_at'=>time());
          
                $insert = $this->db->insert("eprescription",$ins_data); 
                // echo $this->db->last_query();die;
                if($insert)
                {
                     $report=array('status'=>TRUE,'message'=>"Inserted Successfully");
                     return $report;
                }
        
     }
     
     
      function labtests($doctor_id,$patient_id,$appointment_id,$prescription_type,$lab_test_name,$lab_test_description)
    {
        
                   $this->db->where("appointment_id",$appointment_id);
        $chk_row = $this->db->get("patient_prescription")->row();
        if(!empty($chk_row))
        {
             $pres_id =$chk_row->id;   
        }
        else
        {
         
            $array = array('prescription_type'=>$prescription_type,'doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'appointment_id'=>$appointment_id,'created_at'=>time());
            $this->db->insert("patient_prescription",$array);
            // 
            $pres_id =$this->db->insert_id();
            
           
        }
    
                 $ins_data =array('patient_prescription_id'=>$pres_id,'lab_test_name'=>$lab_test_name,'lab_test_description'=>$lab_test_description,'lab_test_created_at'=>time());
          
                $insert = $this->db->insert("lab_tests",$ins_data); 
                // echo $this->db->last_query();die;
                if($insert)
                {
                     $report=array('status'=>TRUE,'message'=>"Inserted Successfully");
                     return $report;
                }
        
     }
     
      function lab_tests($id,$lab_test_name,$lab_test_description,$lab_test_created_at)
    {
                 $this->db->where('id',$id);
                //  $this->db->where('doctor_id',$doctor_id);
                //  $this->db->where('patient_id',$patient_id);
                //  $this->db->where('appointment_id',$appointment_id);
         $res =  $this->db->count_all_results('doctor_appointments'); 
        
            if($res>0)
            {
                         $this->db->where('patient_prescription_id',$id);
                        //  $this->db->where('doctor_id',$doctor_id);
                        //  $this->db->where('patient_id',$patient_id);
          
                 $row =  $this->db->count_all_results('lab_tests'); 
                 
                 if($row>0){
                     
                             $upd_data =array('lab_test_name'=>$lab_test_name,'lab_test_description'=>$lab_test_description,'lab_test_created_at'=>$lab_test_created_at);
                             $wr = array('patient_prescription_id'=>$id);
                             $upd = $this->db->update("lab_tests",$upd_data,$wr); 
                             
                             if($upd)
                                 {
                                   $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                                    return $report;
                                  }
                           }
                     else 
                          {
                              $ins_data =array('patient_prescription_id'=>$id,'lab_test_name'=>$lab_test_name,'lab_test_description'=>$lab_test_description,'lab_test_created_at'=>$lab_test_created_at);
                                
                              $insert = $this->db->insert("lab_tests",$ins_data); 
                              if($insert)
                                 {
                                   $report=array('status'=>TRUE,'message'=>"Inserted Successfully");
                                    return $report;
                                  }
                           }
            }
            else{
                    $report=array('status'=>FALSE,'message'=>"Appointment details doesnot exist");
                     return $report; 
               }
     }


       function prescription_single($id,$doctor_id,$patient_id,$appointment_id,$prescription_id,$chief_complaints,$diagnosis,$advice,$investigation,$follow_up,$handwritten_prescription,$prescription_created_at)
    {   
                $this->db->where('id',$id);
                $this->db->where('prescription_id',$prescription_id); 
                $this->db->where('appointment_id',$appointment_id);
                
        $res =  $this->db->count_all_results('medication');
        if($res>0)
        {
            
         
             if($prescription_id==4)
            {
                 $upd_data =array('chief_complaints'=>$chief_complaints);
            }
              else if($prescription_id==5)
            {
                 $upd_data =array('diagnosis'=>$diagnosis);
            }
              else if($prescription_id==7)
            {
                 $upd_data =array('advice'=>$advice,);
            }
              else if($prescription_id==8)
            {
                 $upd_data =array('investigation'=>$investigation);
            }
              else if($prescription_id==9)
            {
                 $upd_data =array('follow_up'=>$follow_up);
            }
               else 
            {
                 $upd_data =array('handwritten_prescription'=>$handwritten_prescription);
            }
                     $wr =array('appointment_id'=>$appointment_id);
              $upd = $this->db->update("medication",$upd_data,$wr); 
              
            //   echo $this->db->last_query();die;
           
              
        if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                     return $report;
                }
        }
        else
        {
            if($prescription_id==4)
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'chief_complaints'=>$chief_complaints);
            }
              else if($prescription_id==5)
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'diagnosis'=>$diagnosis);
            }
             
              else if($prescription_id==7)
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'advice'=>$advice);
            }
              else if($prescription_id==8)
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'investigation'=>$investigation);
            }
              else if($prescription_id==9)
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'follow_up'=>$follow_up);
            }
         
                else 
            {
                 $ins_data =array('doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'prescription_id'=>$prescription_id,'appointment_id'=>$appointment_id,'handwritten_prescription'=>$handwritten_prescription);
            }
            
                $insert = $this->db->insert("medication",$ins_data); 
                if($insert)
                {
                     $report=array('status'=>TRUE,'message'=>"Inserted Successfully");
                     return $report;
                }
        }
        
       
         
     }
     
       function prescription_status_check($appointment_id)
    {
                     $this->db->where('appointment_id',$appointment_id);
                     $this->db->where('prescription_type','diagnosis');
             $row =  $this->db->get('patient_prescription')->row();
            //  print_r($row);die;
            //  echo $this->db->last_query();die;
        // $res =  $this->db->count_all_results('patient_prescription');
        if($row>0)
        {
        //   print_R($row);die;
            
            if($row->chief_complaints!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                
                $chief_complaints =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $chief_complaints=array('status' =>FALSE,'last_updated'=>'');
                
            }
            
            
            if($row->diagnosis!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $diagnosis =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $diagnosis=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->advice!='')
            {
                 $last_updated = date("d-m-Y h:i A",$row->created_at);
                $advice =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $advice=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->investigation!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $investigation =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $investigation=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->followup!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $follow_up =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $follow_up=array('status' =>FALSE,'last_updated'=>'');
            }
            
               $ins_data = array('status' =>TRUE,'chief_complaints'=>$chief_complaints,'diagnosis'=>$diagnosis,'advice'=>$advice,'investigation'=>$investigation,'follow_up'=>$follow_up);
               
               return $ins_data;
           
       }
       
       else{
           
            if($row->chief_complaints!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                
                $chief_complaints =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $chief_complaints=array('status' =>FALSE,'last_updated'=>'');
                
            }
            
            
            if($row->diagnosis!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $diagnosis =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $diagnosis=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->advice!='')
            {
                 $last_updated = date("d-m-Y h:i A",$row->created_at);
                $advice =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $advice=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->investigation!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $investigation =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $investigation=array('status' =>FALSE,'last_updated'=>'');
            }
            
            if($row->followup!='')
            {
                $last_updated = date("d-m-Y h:i A",$row->created_at);
                $follow_up =  array('status' =>TRUE,'last_updated'=>$last_updated);
            }
            else
            {
                $follow_up=array('status' =>FALSE,'last_updated'=>'');
            }
            
               $ins_data = array('chief_complaints'=>$chief_complaints,'diagnosis'=>$diagnosis,'advice'=>$advice,'investigation'=>$investigation,'follow_up'=>$follow_up);
               
               return $ins_data;
           
     
       }
    }


    function past_prescription_count($prescription_id,$appointment_id) 
    {
                // $this->db->where('doctor_id',$doctor_id);
                // $this->db->where('patient_id',$patient_id);
                $this->db->where('prescription_id',$prescription_id);
                $this->db->where('appointment_id',$appointment_id);
                
        $res =  $this->db->count_all_results('medication_values'); 
        
        // echo $this->db->last_query();die;
        
        if($res)
        {
                 return array("status"=>TRUE,'data'=>$res);
        }
        else
        {
            return array("status"=>FALSE,'message'=>"No data Found");
        }
        
    }
    
        function past_prescription_result($prescription_id,$appointment_id)
    {
                $this->db->select('*');
                // $this->db->where('doctor_id',$doctor_id);
                // $this->db->where('patient_id',$patient_id);
                  $this->db->where('appointment_id',$appointment_id);
                 
                $this->db->where('prescription_id',$prescription_id);
               
        $res =  $this->db->get('medication_values')->result(); 
        
        if($res)
        {
                 return array("status"=>TRUE,'data'=>$res);
        }
        else
        {
            return array("status"=>FALSE,'message'=>"No data Found");
        }
        
    }

    function prescription()
    {
                           $this->db->select('*');
                          
        $prescription  =   $this->db->get('prescription')->result();
        
        
        
        // echo $this->db->last_query();die;
       
        if($prescription>0)
        {
                 return array("status"=>TRUE,'data'=>$prescription);
        }
        else
        {
            return array("status"=>FALSE,'message'=>"No data found");
        }
        
    }
    
      function doctorLocation($doctor_id,$latitude,$longitude)
    {

                           $this->db->where('id',$doctor_id);
                           $this->db->where('latitude',$latitude);
                           $this->db->where('longitude',$longitude);
        $validate_doctor = $this->db->count_all_results('doctors');
       
        if($validate_doctor>0)
        {
                return array('status' =>TRUE,'message'=>"Location Validated");
        }
        else
        {
            return array('status' =>FALSE, 'message'=>"Doctor Doesn't registered with the given Location");
        }
        
    }

    function update_bankdetails($doctor_id,$bank_name,$account_holder_name,$account_number,$retype_account_number,$ifsc_code)
    {
         if($account_number != $retype_account_number)
                {
                     $report=array('status'=>FALSE,'message'=>"Account Number  Doesnot Match");
                     return $report;
                }   
     
        $this->db->where('doctor_id',$doctor_id);
        $res = $this->db->count_all_results('doctor_bank_details');
        if($res>0)
        {
         
         
                      $upd_data = array('bank_name'=>$bank_name,'account_holder_name'=>$account_holder_name,'account_number'=>$account_number,'retype_account_number'=>$retype_account_number,'ifsc_code'=>$ifsc_code);
                      
                    //   print_r($upd_data);die;
                      
                      $wr =array('doctor_id'=>$doctor_id);
              $upd = $this->db->update("doctor_bank_details",$upd_data,$wr);  
                if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                     return $report;
                }
        }
        else
        {
            
            if($bank_name!="")
            {
                $bank_name=$bank_name;
            }
            else
            {
                $bank_name="";
            }
              if($account_holder_name!="")
            {
                $account_holder_name=$account_holder_name;
            }
            else
            {
                $account_holder_name="";
            }
              if($account_number!="")
            {
                $account_number=$account_number;
            }
            else
            {
                $account_number="";
            }
              if($retype_account_number!="")
            {
                $retype_account_number=$retype_account_number;
            }
            else
            {
                $retype_account_number="";
            }
              if($ifsc_code!="")
            {
                $ifsc_code=$ifsc_code;
            }
            else
            {
                $ifsc_code="";
            }
                $ins_data = array('doctor_id'=>$doctor_id,'bank_name'=>$bank_name,'account_holder_name'=>$account_holder_name,'account_number'=>$account_number,'retype_account_number'=>$retype_account_number,'ifsc_code'=>$ifsc_code);
                      
                   
                $upd = $this->db->insert("doctor_bank_details",$ins_data); 
                if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Inserted Successfully");
                     return $report;
                }
        }
     }
     
     
      function update_slots($id,$morning_start_time,$morning_end_time,$afternoon_start_time,$afternoon_end_time,$evening_start_time,$evening_end_time)
    {
    
     
        $this->db->where('id',$id);
        $res = $this->db->count_all_results('doctors');
        
        
   
        if($res>0)
        {

                         
                         
                         $morning_start_time1 = $morning_start_time / 1000;
                      $morning_start_time = date("H:i:s",$morning_start_time1);

                          $morning_end_time1 = $morning_end_time / 1000;
                      $morning_end_time = date("H:i:s",$morning_end_time1);

                          $afternoon_start_time1 = $afternoon_start_time / 1000;
                      $afternoon_start_time = date("H:i:s",$afternoon_start_time1);

                          $afternoon_end_time1 = $afternoon_end_time / 1000;
                      $afternoon_end_time = date("H:i:s",$afternoon_end_time1);

                          $evening_start_time1 = $evening_start_time / 1000;
                      $evening_start_time = date("H:i:s",$evening_start_time1);
                          $evening_end_time1 = $evening_end_time / 1000;
                      $evening_end_time = date("H:i:s",$evening_end_time1);

                      $upd_data = array('morning_start_time'=>$morning_start_time,'morning_end_time'=>$morning_end_time,'afternoon_start_time'=>$afternoon_start_time,'afternoon_end_time'=>$afternoon_end_time,'evening_start_time'=>$evening_start_time,'evening_end_time'=>$evening_end_time);
                      
                    //   print_r($upd_data);die;
                      
                      $wr =array('id'=>$id);
              $upd = $this->db->update("doctors",$upd_data,$wr); 
              
         
                if($upd)
                {
                     $report=array('status'=>TRUE,'message'=>"Updated Successfully");
                     return $report;
                }
        }
        else
        {
             $report=array('status'=>FALSE,'message'=>"DATA NOT FOUND");
             return $report;
        }
     }
     
     
        function get_bankdetails($doctor_id)
    {
       
                        $this->db->select('bank_name,account_holder_name,account_number,ifsc_code');
                        $this->db->where('doctor_id',$doctor_id); 
                          
        $bank_det  =   $this->db->get('doctor_bank_details')->row();
        
        
        
        // echo $this->db->last_query();die;
       
        if($bank_det>0)
        {
                 return array("status"=>TRUE,'data'=>$bank_det);
        }
        else
        {
            return array("status"=>FALSE,'message'=>"No data found"); 
        }
   
       
     }
     
          function get_slots($id)
    {
       
                        $this->db->select('morning_start_time,morning_end_time,afternoon_start_time,afternoon_end_time,evening_start_time,evening_end_time');
                        $this->db->where('id',$id); 
                          
        $time_slots  =   $this->db->get('doctors')->row();
        
        if($time_slots->morning_start_time=='00:00:00' ){
            
            $time_slots->morning_start_time = '';
        }
        else{
            $time_slots->morning_start_time = strtotime($time_slots->morning_start_time)*1000;
        }
        
         if($time_slots->morning_end_time=='00:00:00' ){
            
            $time_slots->morning_end_time = '';
        }
        else{
            $time_slots->morning_end_time = strtotime($time_slots->morning_end_time)*1000;
        }
         if($time_slots->afternoon_start_time=='00:00:00' ){
            
            $time_slots->afternoon_start_time = '';
        }
        else{
            $time_slots->afternoon_start_time = strtotime($time_slots->afternoon_start_time)*1000;
        }
         if($time_slots->afternoon_end_time=='00:00:00' ){
            
            $time_slots->afternoon_end_time = '';
        }
        else{
            $time_slots->afternoon_end_time = strtotime($time_slots->afternoon_end_time)*1000;
        }
         if($time_slots->evening_start_time=='00:00:00' ){
            
            $time_slots->evening_start_time = '';
        }
        else{
            $time_slots->evening_start_time = strtotime($time_slots->evening_start_time)*1000;
        }
         if($time_slots->evening_end_time=='00:00:00' ){
            
            $time_slots->evening_end_time = '';
        }
        else{
            $time_slots->evening_end_time = strtotime($time_slots->evening_end_time)*1000;
        }
        
        // echo $this->db->last_query();die;
       
        if($time_slots>0)
        {
            return array("status"=>TRUE,'data'=>$time_slots);
        }
        else
        {
            return array("status"=>FALSE,'message'=>"No data found"); 
        }
   
       
     }
     
      function delete_bankdetails($doctor_id)
    {
   
                $this->db->where('doctor_id',$doctor_id);
       $res =   $this->db->delete('doctor_bank_details');
    //   echo $this->db->last_query();die;
            if($res)
            {
                     $report=array('status'=>TRUE,'message'=>"Bank Details Deleted Successfully");
                     return $report;
            }
               else 
            {
                     $report=array('status'=>FALSE,'message'=>"No Data Found");
                     return $report;
            }
            
     }
     
     
        
    
        
    function checkLogin($username, $password, $token)
    {
        $this->db->where('password', $password);
        $this->db->group_start();
        $this->db->where('mobile_number', $username);
        $this->db->or_where('email', $username);
        $this->db->group_end();
        $chk = $this->db->get('doctors');
        
        if ($chk->num_rows() > 0) {
            $row = $chk->row();
            if ($row->doctor_login_status == 'inactive') {
                return array('status' => FALSE, 'message' => "Your account is inactivated, Please contact admin");
            } else if ($row->doctor_login_status == 'active') {

                if ($row->doctor_image != '') {
                    $img = base_url() . "uploads/doctors/" . $row->doctor_image;
                } else {
                    $img = base_url() . "uploads/noproduct.png";
                }

                // If token is provided from header/app, update it in DB
                if (!empty($token)) {
                    $this->db->update("doctors", array('token' => $token), array('id' => $row->id));
                }

                $res = array(
                    'status' => TRUE,
                    'doctor_id' => $row->id,
                    'mobile_number' => $row->mobile_number,
                    'email' => $row->email,
                    'doctor_name' => $row->doctor_name,
                    'hospital_name' => $row->hospital_name,
                    'address' => $row->address,
                    'doctor_image' => $img
                );

                return $res;
            }
        } else {
            return array('status' => FALSE, 'message' => "Invalid Mobile or Password");
        }
    }

    function consults_for_today($doctor_id){
        $date=date("Y-m-d");
        $this->cancelledTheAppointment();
        $today_count = $this->pending_appointment($date,$doctor_id);
       $total_appointments = $this->total_appointments($date,$doctor_id);
       
        $completed = $this->completed($date,$doctor_id);
        // $total_appointments = $this->total($date,$doctor_id);
        $pending_acceptance_count = $this->pending_count($date,$doctor_id);

       // print_r($pending_acceptance_count); die;
        $upcoming_schedules = $this->accept_count($doctor_id);
        // echo $this->db->last_query();die;
        $notifications = $this->notification_count($doctor_id);
        
      
        // if($completed>$today_count)
        // {
        //     $pending = $completed-$today_count;
        // }
        // else
        // {
        //     $pending = $today_count-$completed;
        // }
        //$total_appointments = $completed + $today_count;
        
       // $pending = $total_appointments - $completed;
        
        $ar = array('status' =>TRUE,'total_appointments'=>$total_appointments,'completed_appointments'=>$completed,'pending_appointments'=>$pending_acceptance_count,'pending_acceptance_count'=>$pending_acceptance_count,'upcoming_schedules_count'=>$upcoming_schedules,'notifications_count'=>$notifications);
        return $ar;
    }


    function cancelledTheAppointment()
    {
       
                    $this->db->where("doctor_status","active");
                    $this->db->or_where("doctor_status","accept");
            $data = $this->db->get("doctor_appointments")->result();
            foreach ($data as $value) 
            {
                $patient_id=$value->patient_id;
                $appointment_id = $value->id;
                $reason = "Cancelled by admin";
                $comments = "Appointment Cancelled by admin";

                $created_date = $value->date;

                $end_date = date('Y-m-d', strtotime($created_date. ' + 3 days')); 
                $c_date = date("Y-m-d");
                
                if($end_date<$c_date)
                {   
                    
                    $data =array('reason'=>$reason,'comments'=>$comments,'doctor_status'=>'reject', 'rejected_by'=>'admin');
                    $where = array('id'=>$appointment_id,'date<'=>$c_date);
                    $table = "doctor_appointments";
                    $res = $this->db->update($table,$data,$where);
                    //echo $this->db->last_query(); die;
                    if($res)
                    {
                        $patient_row = $this->db->where("id",$appointment_id)->get("doctor_appointments")->row();

                        $doctor_row = $this->db->where("id",$patient_row->doctor_id)->get("doctors")->row();

                        /* $otp_message = "Dear ".$doctor_row->doctor_name." your booking no.".$appointment_id." is cancelled by patient Thank and regards DOCTTO Thanks & Regards...! DOCTTO";
                        $template_id = '1407168691897786773';
                        $this->user->send_message($otp_message,$doctor_row->mobile_number,$template_id);*/

                         /*$arr = array('status'=>TRUE,'message'=>"Appointment cancelled successfully");
                        return $arr;   */ 
                    }

                }
            }

        return TRUE;
        
    }
    function pending_appointment($date,$doctor_id){
        $table = "doctor_appointments";
        $this->db->select("id,date,doctor_status");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'accept');
        return $this->db->get($table)->num_rows();
    }



    function total_appointments($date=NULL,$doctor_id){
        $this->db->where("doctor_id",$doctor_id);
        if($date!='')
        {
           $this->db->where("date",$date);
        }
        $offline = $this->db->get('doctor_appointments')->num_rows();
        return $offline;
    }

    function completed($date,$doctor_id){
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'completed');
        $offline = $this->db->get('doctor_appointments')->num_rows();

        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'completed');
        $this->db->where("payment_status",'completed');
        $online = $this->db->get('online_doctor_appointments')->num_rows();

        return $offline + $online;
    } 
    
      function total($date,$doctor_id){
        $table = "doctor_appointments";
        $this->db->select("id,date,doctor_status");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        // $this->db->where("doctor_status",'completed');
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

        $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,appointment_type, 'offline' as source");
        $this->db->where("doctor_id",$doctor_id); 
        $this->db->where("doctor_status",'accept'); 
        if($date!='') { $this->db->where("date",$date); }
        $this->db->order_by("id","desc");
        $offline_data = $this->db->get('doctor_appointments')->result();

        $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,type as appointment_type, 'online' as source");
        $this->db->where("doctor_id",$doctor_id); 
        $this->db->where("doctor_status",'accept'); 
        $this->db->where("payment_status",'completed'); 
        if($date!='') { $this->db->where("date",$date); }
        $this->db->order_by("id","desc");
        $online_data = $this->db->get('online_doctor_appointments')->result();

        $data = array_merge($offline_data, $online_data);
        usort($data, function($a, $b) { return $b->id - $a->id; });
        
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $orig_status=$value->doctor_status;
               
                if ($orig_status=='accept') {
                    $status="Accept";
                }
                elseif ($orig_status=='completed' || $orig_status == 'COMPLETED') {
                    $status="completed";
                }
                elseif ($orig_status=='reject') {
                    $status="Cancelled";
                }
                else {
                    $status = $orig_status;
                }

                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date_disp=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                $patient_name=$value->patient_name;
                $patient_mobile=$value->patient_mobile;
                $appointment_type=$value->appointment_type;
            
                $data_u = $this->db->select('image')->where(array('id'=>$value->patient_id))->get("users")->row();
                $patient_image = ($data_u && !empty($data_u->image)) ? base_url()."uploads/users/".$data_u->image : base_url()."uploads/profile-icon-3.png";
                
                $data1 = $this->db->where(array('id'=>$doctor_id))->get("doctors")->row();
                if ($data1) {
                    $hospital_name = $data1->hospital_name;
                    $doctor_name = $data1->doctor_name;
                    $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                    $designations_implode= $this->get_designation_names_csv($data1->designations); 
                    $blue_tick = $data1->blue_tick;
                    $doctor_rating = $data1->rating;
                    $total_users_reviewed = $data1->rating_count;  
                    
                    $array[]= array(
                        'id'=>$value->id,
                        'hospital_name'=>$hospital_name,
                        'doctor_name'=>$doctor_name,
                        'doctor_image'=>$doctor_image,
                        'designations'=>$designations_implode,
                        'date'=>$date_disp,
                        'time_slot_value'=>$time_slot_value,
                        'created_date'=>$created_date,
                        'consultation_fee'=>$consultation_fee,
                        'doctor_status'=>$status,
                        'patient_name'=>$patient_name,
                        'patient_mobile'=>$patient_mobile,
                        'appointment_type'=>$appointment_type,
                        'patient_image'=>$patient_image,
                        'blue_tick'=>$blue_tick,
                        'doctor_rating'=>$doctor_rating,
                        'total_users_reviewed'=>$total_users_reviewed,
                        'source' => isset($value->source) ? $value->source : 'offline'
                    );  
                }
            }
            if(count($array)>0)
            {   
                // Counting for specific date (merged)
                $target_date = ($date != '') ? $date : date("Y-m-d");

                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$target_date);
                $off_today = $this->db->get("doctor_appointments")->num_rows();

                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$target_date);
                $this->db->where('payment_status', 'completed');
                $on_today = $this->db->get("online_doctor_appointments")->num_rows();

                $today_appointments = $off_today + $on_today;

                $datetime = new DateTime('tomorrow');
                $tomorrow_date = $datetime->format('Y-m-d');

                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$tomorrow_date);
                $off_tom = $this->db->get("doctor_appointments")->num_rows();

                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$tomorrow_date);
                $this->db->where('payment_status', 'completed');
                $on_tom = $this->db->get("online_doctor_appointments")->num_rows();

                $tomorrow_appointments = $off_tom + $on_tom;

                $ar = array('status' =>TRUE,'data'=>$array,'today_appointments'=>$today_appointments,'tomorrow_appointments'=>$tomorrow_appointments);
                return $ar;
            }
        }
        
        return array('status'=>FALSE,'message'=>"No data found");
    }  

    function patient_management($doctor_id,$doctor_status){

        // Offline records
        $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,appointment_type,created_date, 'offline' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",$doctor_status);
        $this->db->order_by("id","desc");
        $offline_data = $this->db->get("doctor_appointments")->result();

        // // Online records (completed only)
        // $online_data = [];
        // if ($doctor_status == 'completed') {
        //     $this->db->select("id,patient_id,doctor_id,payment_status as doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,type as appointment_type,created_date, 'online' as source");
        //     $this->db->where("doctor_id",$doctor_id);
        //     $this->db->where("payment_status",'completed');
        //     $this->db->order_by("id","desc");
        //     $online_data = $this->db->get("online_doctor_appointments")->result();
        // }

        $data = array_merge($offline_data);
        usort($data, function($a, $b) { return $b->id - $a->id; });

        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                
                $data_u = $this->db->select('image')->where(array('id'=>$value->patient_id))->get("users")->row();
                if($data_u && !empty($data_u->image)){
                      $patient_image = base_url()."uploads/users/".$data_u->image;
                }
                else{
                      $patient_image = base_url()."uploads/profile-icon-3.png";
                }

                $patient_name = $value->patient_name;
                $patient_mobile = $value->patient_mobile;
                $patient_age = $value->patient_age;
                $patient_visiting_purpose = $value->patient_visiting_purpose;
                $appointment_type = $value->appointment_type;
                $orig_status=$value->doctor_status;
                
                if ($orig_status=='completed' || $orig_status == 'COMPLETED') {
                    $display_status="Completed";
                }
                elseif ($orig_status=='reject') {
                    $display_status="Cancelled";
                }
                else {
                    $display_status = $orig_status;
                }

                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                
                $data1 = $this->db->where(array('id'=>$doctor_id))->get("doctors")->row();
                if ($data1) {
                    $hospital_name = $data1->hospital_name;
                    $doctor_name = $data1->doctor_name;
                    $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                    $designations_implode= $this->get_designation_names_csv($data1->designations); 
                    
                    $array[]= array(
                        'id'=>$value->id,
                        'hospital_name'=>$hospital_name,
                        'doctor_name'=>$doctor_name,
                        'doctor_image'=>$doctor_image,
                        'designations'=>$designations_implode,
                        'date'=>$date,
                        'time_slot_value'=>$time_slot_value,
                        'created_date'=>$created_date,
                        'consultation_fee'=>$consultation_fee,
                        'doctor_status'=>$display_status,
                        'patient_name'=>$patient_name,
                        'patient_mobile'=>$patient_mobile,
                        'patient_age'=>$patient_age,
                        'patient_visiting_purpose'=>$patient_visiting_purpose,
                        'appointment_type'=>$appointment_type,
                        'patient_image'=>$patient_image,
                        'source' => isset($value->source) ? $value->source : 'offline'
                    ); 
                }
            }
            if(count($array)>0)
            {   
                return array('status' =>TRUE,'data'=>$array);
            }
        }
        
        return array('status'=>FALSE,'message'=>"No data found");
    }  

      function waiting_accepting($doctor_id,$doctor_status){

        $today = date('Y-m-d');
        $table = "doctor_appointments";
        $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,appointment_type, 'offline' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",$doctor_status);
        if($doctor_status == 'active' || $doctor_status == 'accept'){
            $this->db->where("date >= ", $today);
        }
        $this->db->order_by("id","desc");
        $offline_data = $this->db->get($table)->result();

        // $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,type as appointment_type, 'online' as source");
        // $this->db->where("doctor_id",$doctor_id);
        // $this->db->where("doctor_status",$doctor_status);
        // if($doctor_status == 'active' || $doctor_status == 'accept'){
        //     $this->db->where("date >= ", $today);
        //     $this->db->where("payment_status",'completed');
        // }
        // $this->db->order_by("id","desc");
        // $online_data = $this->db->get('online_doctor_appointments')->result();

        $data = array_merge($offline_data);
        usort($data, function($a, $b) { return $b->id - $a->id; });
        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                $doctor_status=$value->doctor_status;
               //echo $doctor_status; die;
                if ($doctor_status=='active') {
                    $status="Need to Accept";
                } elseif ($doctor_status=='accept') {
                    $status="Accepted";
                } elseif ($doctor_status=='completed' || $doctor_status=='COMPLETED') {
                    $status="Completed";
                } elseif ($doctor_status=='reject') {
                    $status="Cancelled";
                } else {
                    $status=$doctor_status;
                }
                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                $patient_name=$value->patient_name;
                $patient_mobile=$value->patient_mobile;
                $patient_age=$value->patient_age;
                $patient_visiting_purpose=$value->patient_visiting_purpose;
                $appointment_type=$value->appointment_type;
                
             $image = $this->db->select('image')->where(array('id'=>$value->patient_id))->get("users")->row()->image;
                
                
                if(!empty($image)){
                $patient_image = base_url()."uploads/users/".$image;
                }
                
                else{
                       $patient_image = base_url()."uploads/profile-icon-3.png";
                }
                
                
                
                /*if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } */
                $table_doctor = "doctors";
                $data1 = $this->db->where(array('id'=>$doctor_id))->get($table_doctor)->row();
                $hospital_name = $data1->hospital_name;
                $doctor_name = $data1->doctor_name;
                $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                $designations = $data1->designations;               
                $designations_implode= $this->get_designation_names_csv($data1->designations); 
                
                $array[]= array('id'=>$value->id,'hospital_name'=>$hospital_name,'doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'designations'=>$designations_implode,'date'=>$date,'time_slot_value'=>$time_slot_value,'created_date'=>$created_date,'consultation_fee'=>$consultation_fee,'doctor_status'=>$status,'patient_name'=>$patient_name,'patient_mobile'=>$patient_mobile,'patient_age'=>$patient_age,'patient_visiting_purpose'=>$patient_visiting_purpose,'appointment_type'=>$appointment_type,'patient_image'=>$patient_image); 
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

        $this->db->select("id,doctor_id,doctor_status,date, time_slot_name, time_slot_value,consultation_fee,created_date,patient_id,patient_name,appointment_type, 'offline' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->order_by("id","desc");
        $offline_data = $this->db->get('doctor_appointments')->result();

        $this->db->select("id,doctor_id,doctor_status,date, time_slot_name, time_slot_value,consultation_fee,created_date,patient_id,patient_name,type as appointment_type, 'online' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("payment_status", 'completed');
        $this->db->order_by("id","desc");
        $online_data = $this->db->get('online_doctor_appointments')->result();

        $data = array_merge($offline_data, $online_data);

        // Sort by id desc after merge if needed, but array_merge is okay for now
        usort($data, function($a, $b) {
            return $b->id - $a->id;
        });

        if(count($data)>0){
            $array=[];
            foreach ($data as $value) {
                
               $data_u = $this->db->select('image')->where('id',$value->patient_id)->get('users')->row();
               if($data_u && $data_u->image!='')
               {
                   $patient_image = base_url()."uploads/users/".$data_u->image;
               }
               else{
                   $patient_image =  base_url()."uploads/profile-icon-3.png";
               }
                
                $orig_status=$value->doctor_status;
                if($orig_status=='active' || $orig_status == 'pending'){
                    $status="Waiting for Doctor Acceptancy";
                }
                elseif ($orig_status=='accept') {
                    $status="Booking Accepted";
                }
                elseif ($orig_status=='completed' || $orig_status == 'COMPLETED') {
                    $status="completed";
                }
                elseif ($orig_status=='reject') {
                    $status="Cancelled";
                }
                else {
                    $status = $orig_status;
                }

                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;

                
                $data1 = $this->db->where(array('id'=>$doctor_id))->get("doctors")->row();
                if ($data1) {
                    $hospital_name = $data1->hospital_name;
                    $doctor_name = $data1->doctor_name;
                    $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                    $designations_implode= $this->get_designation_names_csv($data1->designations); 
                    
                    $patient_name=$value->patient_name;
                    $appointment_type=$value->appointment_type;
                           
                    $blue_tick= $data1->blue_tick ;
                    $doctor_rating = $data1->rating;
                    $total_users_reviewed = $data1->rating_count;
                    
                    $array[]= array(
                        'id'=>$value->id,
                        'hospital_name'=>$hospital_name,
                        'doctor_name'=>$doctor_name,
                        'doctor_image'=>$doctor_image,
                        'designations'=>$designations_implode,
                        'date'=>$date,
                        'time_slot_value'=>$time_slot_value,
                        'created_date'=>$created_date,
                        'consultation_fee'=>$consultation_fee,
                        'blue_tick'=>$blue_tick,
                        'doctor_rating'=>$doctor_rating,
                        'total_users_reviewed'=>$total_users_reviewed, 
                        'doctor_status'=>$status,
                        'patient_name'=>$patient_name,
                        'appointment_type'=>$appointment_type,
                        'patient_image'=>$patient_image,
                        'source' => isset($value->source) ? $value->source : 'offline'
                    );  
                }
            }
            if(count($array)>0)
            {   
                // Today's total consultation fee (merged)
                $today = date("Y-m-d");
                
                $this->db->select("SUM(consultation_fee) as fee");
                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$today);
                $this->db->where("doctor_status",'completed');
                $offline_fee = $this->db->get("doctor_appointments")->row()->fee;

                $this->db->select("SUM(consultation_fee) as fee");
                $this->db->where("doctor_id",$doctor_id);
                $this->db->where("date",$today);
                $this->db->where("payment_status",'completed');
                $online_fee = $this->db->get("online_doctor_appointments")->row()->fee;

                $total_today_fee = (float)$offline_fee + (float)$online_fee;

                $ar = array('status' =>TRUE,'data'=>$array,'today_consultation_fee'=>(string)$total_today_fee);
                return $ar;
            }
        }
        
        return array('status'=>FALSE,'message'=>"No data found");
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
                /*if($value->time_slot_name=='morning'){
                     $time_slot_value=$time_slot_value.":00 AM";
                }   
                else if($value->time_slot_name=='afternoon'){
                     $time_slot_value=$time_slot_value.":00 PM";
                } 
                elseif ($value->time_slot_name=='evening') {
                     $time_slot_value=$time_slot_value.":00 PM";
                } */
                
                $table_doctor = "doctors";
                $data1 = $this->db->where(array('id'=>$doctor_id))->get($table_doctor)->row();
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


    function appointmentDetails($doctor_id, $appointment_id)
    {
        // Try offline first
        $this->db->select("*, 'offline' as source");
        $this->db->where("doctor_id", $doctor_id);
        $this->db->where("id", $appointment_id);
        $data = $this->db->get("doctor_appointments")->row();

        if (!$data) {
            // Try online (paid only)
            $this->db->select("*, doctor_status, type as appointment_type, 'online' as source");
            $this->db->where("doctor_id", $doctor_id);
            $this->db->where("id", $appointment_id);
            $this->db->where('payment_status', 'completed');
            $data = $this->db->get("online_doctor_appointments")->row();
        }

        if ($data) {
            // Patient details
            $data_u = $this->db->select('image')->where(array('id' => $data->patient_id))->get("users")->row();
            $patient_image = ($data_u && !empty($data_u->image)) ? base_url() . "uploads/users/" . $data_u->image : base_url() . "uploads/profile-icon-3.png";
            $data->patient_image = $patient_image;

            // Doctor details
            $doctor_details = $this->db->where(array('id' => $data->doctor_id))->get("doctors")->row();
            if ($doctor_details) {
                $data->blue_tick = $doctor_details->blue_tick;
                $data->doctor_rating = $doctor_details->rating;
                $data->total_users_reviewed = $doctor_details->rating_count;
                $data->doctor_mobile_number = $doctor_details->mobile_number;
                $data->doctor_image = base_url() . "uploads/doctors/" . $doctor_details->doctor_image;
                $data->hospital_name = $doctor_details->hospital_name;
                $data->doctor_name = $doctor_details->doctor_name;
                $data->address = $doctor_details->address;

                $designations = $doctor_details->designations;
                $ex_designations = explode(',', $designations);
                $designations_name = [];
                foreach ($ex_designations as $designation_value) {
                    $designations_row = $this->db->where(array('id' => $designation_value))->get("designations")->row();
                    if ($designations_row) {
                        $designations_name[] = $designations_row->name;
                    }
                }
                $data->designation = implode(",", $designations_name);
            }

            $data->date = date("d-m-Y", strtotime($data->date));

            if ($data->doctor_status == 'reject') {
                if (isset($data->rejected_by) && $data->rejected_by == "Doctor") {
                    $data->rejected_by = "You";
                } else {
                    $data->rejected_by = "Patient";
                }
            }

            // Prescription check
            $this->db->where("appointment_id", $appointment_id);
            $chk = $this->db->get("patient_prescription")->row();

            $data->invoice_download_ready = false;
            if ($chk) {
                $this->db->select('id');
                $this->db->where("appointment_id", $appointment_id);
                $pa_pres_id = $this->db->get("patient_prescription")->row();

                if ($pa_pres_id) {
                    $this->db->where("patient_prescription_id", $pa_pres_id->id);
                    $chk_sts = $this->db->get("eprescription")->result();
                    if ($chk_sts) {
                        $data->invoice_download_ready = true;
                    }
                }
            }

            // Lab test check
            $data->labtest_invoice_download_ready = false;
            $this->db->select('id');
            $this->db->where("appointment_id", $appointment_id);
            $pa_pres_id_lab = $this->db->get("patient_prescription")->row();
            if ($pa_pres_id_lab) {
                $this->db->where("patient_prescription_id", $pa_pres_id_lab->id);
                $sts = $this->db->get("lab_tests")->result();
                if ($sts) {
                    $data->labtest_invoice_download_ready = true;
                }
            }

            if (isset($data->completed_date) && $data->completed_date != "0000-00-00 00:00:00" && $data->completed_date != "") {
                $data->completed_date = $data->completed_date;
            } else {
                $data->completed_date = "";
            }

            return array('status' => TRUE, 'data' => $data);
        } else {
            return array('status' => FALSE, 'message' => "No data found");
        }
    }
    
    
       function waitingAppointmentDetails($doctor_id,$appointment_id)
    {
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("id",$appointment_id);
        $this->db->where("doctor_status",'active');
        $data = $this->db->get("doctor_appointments")->row();
        
        if(!$data)
        {
            $this->db->where("doctor_id",$doctor_id);
            $this->db->where("id",$appointment_id);
            $this->db->where("doctor_status",'active');
            $this->db->where("payment_status",'completed');
            $data = $this->db->get("online_doctor_appointments")->row();
        }

        if($data)
        {
        if($data->doctor_status == "active")
        {
            $data->doctor_status = "Need To Accept";
        }
        // echo $this->db->last_query();die;
        
        
        // print_r($data);die;
        
        // getting patient image based on patient id
        
          $image = $this->db->select('image')->where(array('id'=>$data->patient_id))->get("users")->row()->image;
                
                
                if(!empty($image)){
                $patient_image = base_url()."uploads/users/".$image;
                }
                
                else{
                       $patient_image = base_url()."uploads/profile-icon-3.png";
                }
        



        $doctor_details = $this->db->where(array('id'=>$data->doctor_id))->get("doctors")->row();
        
        
        $data->patient_image = $patient_image;

        // $data->doctor_image = base_url()."uploads/shops/".$doctor_details->doctor_image;

        // $data->hospital_name = $doctor_details->hospital_name;

        $data->date = date("d-m-Y",strtotime($data->date));
        // $data->doctor_name = $doctor_details->doctor_name;
        //  $data->address = $doctor_details->address;
        //   $designations = $doctor_details->designations;
        //   $ex_designations = explode(',', $designations);
        //   $designations_name=[];
        //   foreach ($ex_designations as $designation_value) 
        //   {
        //      $designations_name[] = $this->db->where(array('id'=>$designation_value))->get("designations")->row()->name;
        //   }
        // $data->designation = implode(",", $designations_name);
        

                if ($data->doctor_status=='reject') 
                {
                    if($data->rejected_by=="doctor"){
                        $data->rejected_by = "You";
                    }
                    else
                    {
                         $data->rejected_by = "Patient";
                    }

                }


        $ar = array('status' =>TRUE,'data'=>$data);
        return $ar;
           
    }
    else{
          $ar = array('status' =>FALSE,'message'=>"Invalid Details");
         return $ar;
    }
    
    }

    
    

    function notification_count($doctor_id){
        $table = "doctor_notifications";
        $this->db->where("recieved_id",$doctor_id);
        return $this->db->get($table)->num_rows();
       
    }
    
    function pending_count($date,$doctor_id){
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date",$date);
        $this->db->where("doctor_status",'active');
        $offline = $this->db->get('doctor_appointments')->num_rows();

        return $offline;
    }
    
     function accept_count($doctor_id){
        $date = date('Y-m-d');

        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date>=",$date);
        $this->db->where("doctor_status",'accept');
        $offline = $this->db->get('doctor_appointments')->num_rows();

        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("date>=",$date);
        $this->db->where("doctor_status",'accept');
        $this->db->where('payment_status', 'completed');
        $online = $this->db->get('online_doctor_appointments')->num_rows();

        return $offline + $online;
    }
    
    function eprescription_count($appointment_id){

                  $this->db->where('appointment_id',$appointment_id);
          $data = $this->db->count_all_results("patient_prescription");
          if($data>0)
          {
                                  $this->db->where('appointment_id',$appointment_id);
                                  $this->db->where('manual_prescription!=','');
          $manual_prescription = $this->db->count_all_results("patient_prescription");


                      $this->db->where('appointment_id',$appointment_id);
          $response = $this->db->get("patient_prescription")->row();

                                           $this->db->where('patient_prescription_id',$response->id);
                 $eprescription_count   =  $this->db->count_all_results('eprescription');



                                $this->db->where('patient_prescription_id',$response->id);
                 $labtests   =  $this->db->get('lab_tests')->num_rows();

                  $ar = array('status' =>TRUE,'eprescription_count'=>$eprescription_count,'manual_prescription_count'=>$manual_prescription,'lab_tests_count'=>$labtests);
                   return $ar;  
          }
          else
          {
           return array('status' =>TRUE,'message'=>"Invalid appointment ID");
          }

               
                           
      
       
    }
    
    //   function labtests_count($appointment_id){
    //           $this->db->select('id');
    //           $this->db->where('appointment_id',$appointment_id);
    //           $this->db->where('prescription_type','prescription');
    //           $res = $this->db->get('patient_prescription')->row();
        
               
    //   if($res){
    //              $pres_id = $res->id;
    //                         $this->db->where('patient_prescription_id',$pres_id);
    //              $data   =  $this->db->get('lab_tests')->num_rows();
    //             if($data>0)
    //             {
    //                 $data=$data;
    //             }
    //             else
    //             {
    //                 $data=0;
    //             }
    //               $ar = array('status' =>TRUE,'total_count'=>$data);
    //               return $ar;            
    //   }
       
    //   else{
    //               $ar = array('status' =>FALSE,'message'=>"No Data Found");
    //               return $ar;
    //   }
       
    // }
    
    //  function manual_count($appointment_id){
    
    //           $this->db->where('appointment_id',$appointment_id);
    //           $this->db->where('prescription_type','prescription');
    //           $res = $this->db->get('patient_prescription')->num_rows();
        
               
    //   if($res>0){
              
    //               $ar = array('status' =>TRUE,'total_count'=>$res);
    //               return $ar;            
    //   }
       
    //   else{
    //               $ar = array('status' =>FALSE,'message'=>"No Data Found");
    //               return $ar;
    //   }
       
    // }
    
       function prescription_count($appointment_id){
        $table = "patient_prescription";
               $this->db->where("appointment_id",$appointment_id);
               $this->db->where("manual_prescription!=","");
        $data =$this->db->count_all_results("patient_prescription");
        //$this->db->where("doctor_status",'active');
       //echo $this->db->last_query(); die;
        $ar = array('status' =>TRUE,'notification'=>$data);
        return $ar;
    }


    function appointment_cancel($patient_id,$appointment_id,$reason,$comments){
        $data =array('reason'=>$reason,'comments'=>$comments,'doctor_status'=>'reject','rejected_by'=>"Doctor");
        $where = array('id'=>$appointment_id);
        
        $table = 'doctor_appointments';
        $appoint_row = $this->db->where(array('id'=>$appointment_id))->get($table)->row();
        
        if(!$appoint_row) {
            $table = 'online_doctor_appointments';
            $appoint_row = $this->db->where(array('id'=>$appointment_id))->get($table)->row();
        }

        if(!$appoint_row) {
            return array('status'=>FALSE,'message'=>"Invalid appointment");
        }

        $res = $this->db->update($table,$data,$where);
        //echo $this->db->last_query(); die;
        if($res)
        {
                $message="Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is cancelled by doctor.Thanks & Regards...! DOCTTO";
                $title = "Appointment cancelled";
            $this->doNotifications($appointment_id,$appoint_row->doctor_id,$patient_id,$message,$title);

            $otp_message = "Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is cancelled by doctor.Thanks & Regards...! DOCTTO";
            $template_id = '1407168691901670711';
            $this->User->send_message($otp_message,$appoint_row->patient_mobile,$template_id);

            $arr = array('status'=>TRUE,'message'=>"Appointment cancelled successfully");
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Something went wrong");
            return $arr;  
        }
    }

     function appointmentComplete($patient_id,$appointment_id){
        $cdate=date('Y-m-d H:i:s');
        $where = array('id'=>$appointment_id);
        
        $table = 'doctor_appointments';
        $appoint_row = $this->db->where(array('id'=>$appointment_id))->get($table)->row();
        
        // if(!$appoint_row) {
        //     $table = 'online_doctor_appointments';
        //     $appoint_row = $this->db->where(array('id'=>$appointment_id))->get($table)->row();
        //     $data = array('doctor_status'=>'completed');
        // } else {
            $data = array('doctor_status'=>'completed','completed_date'=>$cdate);
        // }

        if(!$appoint_row) {
            return array('status'=>FALSE,'message'=>"Invalid appointment");
        }

        $res = $this->db->update($table,$data,$where);
        //echo $this->db->last_query(); die;
        if($res)
        {
                $message="Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is completed successfully, Please login into your dashboard. Thanks & Regards...! DOCTTO";
                $title = "Appointment completed";
            $this->doNotifications($appointment_id,$appoint_row->doctor_id,$patient_id,$message,$title);

            $otp_message ="Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is completed successfully, Please login into your dashboard. Thanks & Regards...! DOCTTO";
            $template_id = '1407168691893540432';
            $this->User->send_message($otp_message,$appoint_row->patient_mobile,$template_id);

            $arr = array('status'=>TRUE,'message'=>"Appointment completed successfully");
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Something went wrong");
            return $arr;  
        }
    }

    function doNotifications($appointment_id,$sender_id,$recieved_id,$message,$title)
    {
        $date=date('Y-m-d');
            $doc_array = array('appointment_id'=>$appointment_id,'sender_id'=>$sender_id,'recieved_id'=>$recieved_id,'message'=>$message,'created_date'=>$date,'created_at'=>time(),"title"=>$title);
            $ins =$this->db->insert("doctor_notifications",$doc_array);
            if($ins)
            {
                return TRUE;
            }
    }

    function acceptAppointment($doctor_id,$appointment_id){

        $table = '';
        $valid = FALSE;

        $this->db->where('id', $appointment_id);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('doctor_status', 'active');
        $valid = $this->db->get('doctor_appointments')->row();
        if($valid) {
            $table = 'doctor_appointments';
        }

        if (!$valid) {
            $this->db->where('id', $appointment_id);
            $this->db->where('doctor_id', $doctor_id);
            $this->db->where('doctor_status', 'active');
            $valid = $this->db->get('online_doctor_appointments')->row();
            if($valid) {
                $table = 'online_doctor_appointments';
            }
        }

        if (!$valid) {
            return array('status' => FALSE, 'message' => "Invalid appointment or already processed");
        }

        $data  = array('doctor_status' => 'accept');
        $where = array('id' => $appointment_id, 'doctor_id' => $doctor_id);
        $res   = $this->db->update($table, $data, $where);
        //echo $this->db->last_query(); die;
        if($res)
        {
            $appoint_row = $valid;
                $message="Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is accepted successfully, Please login into your dashboard. Thanks & Regards...! DOCTTO";
                $title = "Appointment Accepted";
            $this->doNotifications($appointment_id,$doctor_id,$appoint_row->patient_id,$message,$title);

            $otp_message = "Dear ".$appoint_row->patient_name." your booking no.".$appointment_id." is accepted successfully, Please login into your dashboard. Thanks & Regards...! DOCTTO";
            $template_id = '1407168691889847853';
            $this->User->send_message($otp_message,$appoint_row->patient_mobile,$template_id);

            $arr = array('status'=>TRUE,'message'=>"Appointment accepted successfully");
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Something went wrong");
            return $arr;  
        }
    }

    
     function update_doctor_details($doctor_id,$doctor_name,$doctor_image,$cover_image,$digital_signature,$designations,$experience,$mobile_number,$aboutus,$gender,$doctor_license_no,$voice_call,$video_call,$morning_start_time=null,$morning_end_time=null,$afternoon_start_time=null,$afternoon_end_time=null,$evening_start_time=null,$evening_end_time=null,$bank_name,$account_holder_name,$account_number,$retype_account_number,$ifsc_code,$chat_price=NULL,$specialisation=NULL,$specialist_in=NULL)
     {


    $row = $this->db->where('id',$doctor_id)->get('doctors')->row();
        if($morning_start_time != ''){
            // print_r('daf');die;

           $morning_start_time1 = $morning_start_time / 1000;
           $morning_start_time = date("H:i:s",$morning_start_time1);
        }
        else{

            $morning_start_time = $row->morning_start_time;
            
        }
        if($morning_end_time != ''){
            
            $morning_end_time1 = $morning_end_time / 1000;
            $morning_end_time = date("H:i:s",$morning_end_time1);
        }
        else{

            $morning_end_time = $row->$morning_end_time;
            
        }
        if($afternoon_start_time != ''){

            $afternoon_start_time1 = $afternoon_start_time / 1000;
            $afternoon_start_time = date("H:i:s",$afternoon_start_time1);
        }
        else{

             $afternoon_start_time = $row->afternoon_start_time;
            
        }
        if($afternoon_end_time != ''){
            // print_r('daf');die;
             $afternoon_end_time1 = $afternoon_end_time / 1000;
            $afternoon_end_time = date("H:i:s",$afternoon_end_time1);
        }
        else{
             $afternoon_end_time = $row->afternoon_end_time;
            
        }
        if($evening_start_time != ''){
            // print_r('daf');die;
            $evening_start_time1 = $evening_start_time / 1000;
            $evening_start_time = date("H:i:s",$evening_start_time1);
        }
        else{
            $evening_start_time = $row->$evening_start_time;
            
        }
        if($evening_end_time != ''){
            // print_r('daf');die;
             $evening_end_time1 = $evening_end_time / 1000;
             $evening_end_time = date("H:i:s",$evening_end_time1);
        }
        else{
            $evening_end_time = $row->$evening_start_time;
            
        }
         if($chat_price!='')
         {
          $chat_price=$chat_price;
         }
         else
         {
          $chat_price=0;
         }


        $data = array('doctor_name'=>$doctor_name,'doctor_image'=>$doctor_image,'hospital_image'=>$cover_image,'digital_signature'=>$digital_signature,'designations'=>$designations,'experience'=>$experience,'mobile_number'=>$mobile_number,'aboutus'=>$aboutus,'gender'=>$gender,'doctor_license_no'=>$doctor_license_no,'voice_call'=>$voice_call,'video_call'=>$video_call,'evening_end_time'=>$evening_end_time,'evening_start_time'=>$evening_start_time,'afternoon_end_time'=>$afternoon_end_time,'afternoon_start_time'=>$afternoon_start_time,'morning_end_time'=>$morning_end_time,'morning_start_time'=>$morning_start_time,'chat_price'=>$chat_price,'consultant_fee'=>$chat_price,'specialisation'=>$specialisation,'specialist_in'=>$specialist_in);
        
        // print_r($data); die;
        
        $where = array('id'=>$doctor_id);
        $table = "doctors";
        $res = $this->db->update($table,$data,$where); 
        
        // echo $this->db->last_query(); die;  
        if($res)
        {
            
                $this->db->where('doctor_id',$doctor_id);
                $reslt = $this->db->count_all_results('doctor_bank_details');
                // echo $this->db->last_query();die;
                if($reslt>0)
                {
                    $upd_data = array('bank_name'=>$bank_name,'account_holder_name'=>$account_holder_name,'account_number'=>$account_number,'retype_account_number'=>$retype_account_number,'ifsc_code'=>$ifsc_code);
                    $wr =array('doctor_id'=>$doctor_id);
                    $this->db->update("doctor_bank_details",$upd_data,$wr); 
                }
                else
                {
                    // print_r('da');die;
                        $ins_data = array('doctor_id'=>$doctor_id,'bank_name'=>$bank_name,'account_holder_name'=>$account_holder_name,'account_number'=>$account_number,'retype_account_number'=>$retype_account_number,'ifsc_code'=>$ifsc_code);
                        $this->db->insert("doctor_bank_details",$ins_data); 
                }
            $arr = array('status'=>TRUE,'message'=>"Updated successfully");
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Something went wrong");
            return $arr;  
        }
        
        
    }
    
    
    function check_kyc_details($doctor_id)
   {

            

                              $this->db->where('doctor_id',$doctor_id);
                $bank_det  =  $this->db->count_all_results('doctor_bank_details');
                if($bank_det>0)
                {
                         $status = TRUE;    
                }
                else
                {
                      $status = FALSE;  
                }
    
                
                               $this->db->where('id',$doctor_id);
                $time_slots  = $this->db->get('doctors')->row();
                
               
             
                  if($time_slots->morning_start_time == '00:00:00' || $time_slots->morning_end_time == '00:00:00'&& $time_slots->afternoon_start_time == '00:00:00' || $time_slots->afternoon_end_time == '00:00:00' || $time_slots->evening_start_time == '00:00:00'|| $time_slots->evening_end_time == '00:00:00')
                {
                 
                         $slot_status = FALSE;    
                }
                else
                {
                   
                      $slot_status = TRUE;  
                }
                
                                    $this->db->where('id',$doctor_id);
                $profile_details  = $this->db->get('doctors')->row();
            //   print_r($profile_details);die; 
             
                 if($profile_details->doctor_name == '' && $profile_details->mobile_number == '' && $profile_details->doctor_image == '' || $profile_details->hospital_image == '' || $profile_details->digital_signature == '' || $profile_details->designations == '' || $profile_details->doctor_license_no == '' || $profile_details->experience == '' || $profile_details->aboutus == '' || $profile_details->gender == '' || $profile_details->voice_call == '' || $profile_details->video_call = '') 
                {
                 
                         $profile_status = FALSE;    
                }
                else
                {
                   
                      $profile_status = TRUE;  
                }
                
                                     $this->db->where('id',$doctor_id);
                $profile_verification  =  $this->db->get('doctors')->row();
                
                // print_r($profile_verification);die;
                
                if($profile_verification->doctor_show_status == 'active'){
                    
                     $verification_status = TRUE;
                }
                else{
                    $verification_status = FALSE;
                }
    
                $data = array('bank_details_updated_or_not'=>$status,'time_slots_updated_or_not'=>$slot_status,'profile_details_updated_or_not'=>$profile_status,'profile_verified_or_not'=>$verification_status);
                return array('status'=>TRUE,'data'=>$data);
    }
    
    function doctor_status($doctor_id,$doctor_login_status){

        $data =array('doctor_show_status'=>$doctor_login_status);
        $where = array('id'=>$doctor_id);
        $table = "doctors";
        $res = $this->db->update($table,$data,$where);
        //echo $this->db->last_query(); die;
        if($doctor_login_status=='active')
        {
            $status = "Status Active";
        }
        else if($doctor_login_status=='inactive')
        {
            $status = "Status Inactive";
        }
        if($res)
        {
            $arr = array('status'=>TRUE,'message'=>$status);
            return $arr;    
        }
        else
        {
            $arr = array('status'=>FALSE,'message'=>"Status not updated");
            return $arr;  
        }
    }
    
       function get_doctor_status($doctor_id){
                  $this->db->select('doctor_login_status');
                  $this->db->where('id',$doctor_id);
        $status = $this->db->get('doctors')->row()->doctor_login_status;
        
        // print_r($status);die;
       
            $arr = array('status'=>TRUE,'doctor_status'=>$status);
            return $arr;    
      
    }
    
       function get_manual_prescription($appointment_id){
                  $this->db->select('manual_prescription');
                  $this->db->where('prescription_type','prescription');
                  $this->db->where('appointment_id',$appointment_id);
        $res = $this->db->get('patient_prescription')->row()->manual_prescription;
        
        if($res){
                  $arr = array('status'=>TRUE,'data'=>$res);
                   return $arr; 
        }
             
      else{
                  $arr = array('status'=>FALSE,'message'=>"no data found");
                   return $arr; 
      }
    }
    
    function upload_images(){
        $id= $this->input->post('user_id');
        $images=$this->upload_file('images');
        $fullpath=base_url()."uploads/shops/".$images;
        $data =array('doctor_image'=>$images);
        $where = array('id'=>$id);
        $table = "doctors";
        $res = $this->db->update($table,$data,$where);
        
        $arr = array('status'=>TRUE,'path'=>$images,'fullpath'=>$fullpath);
            return $arr; 
    }

    function uploadHospitalImages()
    {
         $id= $this->input->post('user_id');
        $images=$this->upload_file('images');
        $fullpath=base_url()."uploads/shops/".$images;
        $data =array('hospital_image'=>$images);
        $where = array('id'=>$id);
        $table = "doctors";
        $res = $this->db->update($table,$data,$where);
        
        $arr = array('status'=>TRUE,'path'=>$images,'fullpath'=>$fullpath);
            return $arr; 
    }

     function upload_file($file_name) {


        if($_FILES[$file_name]['name']!='')
            {
                if($_FILES[$file_name]["size"]<'11114374')
                {
                    $upload_path1 = "./uploads/shops";
                    $config1['upload_path'] = $upload_path1;
                    $config1['allowed_types'] = "*";
                    // $config1['allowed_types'] = "*";
                    $config1['max_size'] = "204800000";
                    $img_name1 = strtolower($_FILES[$file_name]['name']);
                    $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
                    $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
                    $this->load->library('upload', $config1);
                    $this->upload->initialize($config1);
                    $this->upload->do_upload($file_name);
                    $fileDetailArray1 = $this->upload->data();
                    // echo $this->upload->display_errors();
                    // die;
                    return $fileDetailArray1['file_name'];
                }
                else
                {
                   return 'false';
                }
            }
            else
            {
                return 'false';
            }
    }
    
    
    function doctors_details($doctor_id){ 
        
        

        $table = "doctors";
        $this->db->select("*");
        $where = array('id'=>$doctor_id);
        
        $value = $this->db->where($where)->get($table);
        $query =  $value->num_rows();

        // Loading subscription model to get current plan
        $this->load->model('subscription_api_model');
        
        if($query>0){
            
               $query = $value->row();
               
               $id = $query->id;
                
                //getting specialisation name based on specialisation id
                                  
                                       $this->db->select('name');
                                       $this->db->where('id',$query->specialisation);
                $specialisation_name = $this->db->get('specialisation')->row()->name;
                $specialist_data = $this->db->query("select * from specialist_in where find_in_set(id,'".$query->specialist_in."')")->result();
       
                $specialist_name=[];
                foreach($specialist_data as $value)
                {
                    $specialist_name[]=$value->specialist_in;
                }

                // $specialist_name=array(implode(",",$specialist_in_array));
                
                $designation_data = $this->db->query("select * from designations where find_in_set(id,'".$query->designations."')")->result();
                $desig_name=[];
                foreach($designation_data as $value)
                {
                    $desig_name[]=$value->name;
                    
                }
                

                
                // $hospital_image = base_url()."uploads/shops/".$query->hospital_image;
                $doctor_image = base_url()."uploads/doctors/".$query->doctor_image;
                   if($query->doctor_image != ''){
                    
                    
                   $doctor_image = base_url()."uploads/doctors/".$query->doctor_image;   
                }
                else{
                
                    $doctor_image = base_url()."uploads/profile-icon-3.png";
                   
                }

                 if($query->hospital_image != ''){
                    
                    
                   $cover_image = base_url()."uploads/doctors/".$query->hospital_image;   
                }
                else{
                
                    $cover_image = base_url()."uploads/profile-icon-3.png";
                }
                if($query->digital_signature != ''){
                    
                    
                   $digital_signature = base_url()."uploads/doctors/".$query->digital_signature;   
                }
                else{
                
                    $digital_signature = '';
                }
                // $digital_signature = base_url()."uploads/doctors/".$query->digital_signature; 
                $doctor_name = $query->doctor_name;
                $mobile_number = $query->mobile_number;
                $email = $query->email;
                $license = $query->doctor_license_no;
                $experience = $query->experience;
                $voice_call = $query->voice_call;
                $video_call = $query->video_call;
                $chat_price = $query->chat_price;
                $aboutus = $query->aboutus;
                if($query->morning_start_time!='00:00:00')
                {
                    $morning_start_time = strtotime($query->morning_start_time)*1000;
                }
                else
                {
                    $morning_start_time = "";
                }
                   if($query->morning_end_time!='00:00:00')
                {
                    $morning_end_time = $query->morning_end_time;
                }
                else
                {
                    $morning_end_time = "";
                }
                   if($query->afternoon_start_time!='00:00:00')
                {
                    $afternoon_start_time = $query->afternoon_start_time;
                }
                else
                {
                    $afternoon_start_time = "";
                }
                   if($query->afternoon_end_time!='00:00:00')
                {
                    $afternoon_end_time = $query->afternoon_end_time;
                }
                else
                {
                    $afternoon_end_time = "";
                }
                   if($query->evening_start_time!='00:00:00')
                {
                    $evening_start_time = $query->evening_start_time;
                }
                else
                {
                    $evening_start_time = "";
                }
                   if($query->evening_end_time!='00:00:00')
                {
                    $evening_end_time = $query->evening_end_time;
                }
                else
                {
                    $evening_end_time = "";
                }
                
                
                $morning_end_time = strtotime($query->morning_end_time)*1000;
                $afternoon_start_time = strtotime($query->afternoon_start_time)*1000;
                $afternoon_end_time = strtotime($query->afternoon_end_time)*1000;
                $evening_start_time = strtotime($query->evening_start_time)*1000; 
                $evening_end_time = strtotime($query->evening_end_time)*1000;
                $gender = $query->gender;
                $blue_tick  = $query->blue_tick;
    
             
                $doctor_rating = $query->rating;
                $users_rating_count = $query->rating_count;
                $doctor_show_status = $query->doctor_show_status;
                // $designations_in = $this->get_designation_names_csv($designations_ids); 

                
               
                $table1 = "doctor_admin_comission";
                $this->db->select("cat_id");
                $this->db->where('doctor_id',$doctor_id);
                $data1 = $this->db->get($table1)->result();
                
                
                $cat_array=[];
                $cat_id_array=[];
                foreach ($data1 as $value1){
                  //$cat_id = $value1->cat_id;
                        $where = array('id'=>$value1->cat_id);
                  $data2 = $this->db->where($where)->get('doctor_categories')->row();
                    if($data2->category_name!='')
                    {
                         $cat_array[]=$data2->category_name;
                    }
                 
                  //$cat_id_array[]=$data2->id;
                  
                }

                //$cat_array1 = implode(",", $cat_array);
                
                // Get Subscription Info
                $subscription = $this->subscription_api_model->get_my_subscription($id, 'doctor');
                $sub_plan_name = $subscription ? $subscription->plan_name : "No Active Plan";
                $sub_status = $subscription ? $subscription->status : "inactive";
                $sub_plan_price = $subscription ? $subscription->plan_price : 0;

                $remaining_days = "No Active Plan";
                if ($subscription && !empty($subscription->end_at)) {
                    $formatted_start = !empty($subscription->start_at) ? date('d M Y', strtotime($subscription->start_at)) : '';
                    $formatted_end = date('d M Y', strtotime($subscription->end_at));
                    $remaining_days = $formatted_end;
                }
                
                $array = array('id'=>$id,'doctor_image'=>$doctor_image,'cover_image'=>$cover_image,'digital_signature'=>$digital_signature,'doctor_name'=>$doctor_name,'mobile_number'=>$mobile_number,'email'=>$email,'specialisation'=>$specialisation_name,'specialist_in'=>$specialist_name,'license'=>$license,'designations'=>$desig_name,'experience'=>$experience,'voice_call'=>$voice_call,'video_call'=>$video_call,'chat_price'=>$chat_price,'aboutus'=>$aboutus,'morning_start_time'=>$morning_start_time,'morning_end_time'=>$morning_end_time,'afternoon_start_time'=>$afternoon_start_time,'afternoon_end_time'=>$afternoon_end_time,'evening_start_time'=>$evening_start_time,'evening_end_time'=>$evening_end_time,'category_name'=>$cat_array,'blue_tick'=>$blue_tick,'doctor_rating'=>$doctor_rating,'total_users_reviewed'=>$users_rating_count,'doctor_show_status'=>$query->doctor_show_status,'gender'=>$gender, 'subscription_plan_name' => $sub_plan_name, 'subscription_plan_price' => $sub_plan_price, 'subscription_status' => $sub_status, 'remaining_days' => $remaining_days);

  
            $ar = array('status' =>TRUE,'data'=>$array);
            return $ar;
        }
        else
        {
            $ar = array('status'=>FALSE,'message'=>"No data found");
            return $ar;
        }
    }   

    function login_verification($user_id,$type)
    {
        if($type=='doctor')
        {
            $doctors = $this->db->where(array('id'=>$user_id,'doctor_login_status'=>'active'))->get('doctors')->num_rows();
            if($doctors>0)
            {
                $ar = array('status'=>TRUE);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"Your account is inactivated, please contact admin");
                return $ar;
            }
        }
        else
        {
            $doctors = $this->db->where(array('id'=>$user_id,'vendor_verification_status'=>1))->get('vendor_shop')->num_rows();
            if($doctors>0)
            {
                $ar = array('status'=>TRUE);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"Your account is inactivated, please contact admin");
                return $ar;
            }
        }
    }
    

    function earnings($doctor_id,$start_date=null,$end_date=null,$filter_status=NULL)
    {
        // Offline filters
        $this->db->select("id,patient_id,doctor_id,doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,appointment_type, 'offline' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("doctor_status",'completed');
        
        if($filter_status=='daily') {
            $this->db->where("date", date("Y-m-d"));
        } else if($filter_status=='weekly') {
            $this->db->where("date>=", date("Y-m-d",strtotime('monday this week')));
            $this->db->where("date<=", date("Y-m-d",strtotime("sunday this week")));
        } else if($filter_status=='monthly') {
            $this->db->like("date", date("Y-m"),'both');
        } else if($filter_status=='other' && $start_date!='') {
            $this->db->where("date>=",$start_date);
            $this->db->where("date<=",$end_date);
        }
        $offline_data = $this->db->get("doctor_appointments")->result();

        // Online filters
        $this->db->select("id,patient_id,doctor_id,payment_status as doctor_status,date,time_slot_name,time_slot_value,consultation_fee,patient_name,patient_mobile,patient_age,patient_visiting_purpose,type as appointment_type, 'online' as source");
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("payment_status",'completed');
        
        if($filter_status=='daily') {
            $this->db->where("date", date("Y-m-d"));
        } else if($filter_status=='weekly') {
            $this->db->where("date>=", date("Y-m-d",strtotime('monday this week')));
            $this->db->where("date<=", date("Y-m-d",strtotime("sunday this week")));
        } else if($filter_status=='monthly') {
            $this->db->like("date", date("Y-m"),'both');
        } else if($filter_status=='other' && $start_date!='') {
            $this->db->where("date>=",$start_date);
            $this->db->where("date<=",$end_date);
        }
        $online_data = $this->db->get("online_doctor_appointments")->result();

        $data = array_merge($offline_data, $online_data);
        usort($data, function($a, $b) { return $b->id - $a->id; });
        
        if(count($data)>0){
            $array=[];
            $total_consultation_fee=0;
            foreach ($data as $value) {
                $created_date=date("d M h:i A",strtotime($value->created_date));
                $doctor_id=$value->doctor_id;
                $date_disp=date("d M,Y",strtotime($value->date)); 
                $consultation_fee = $value->consultation_fee;
                $time_slot_value=$value->time_slot_value;
                $time_slot_name=$value->time_slot_name;
                $patient_name=$value->patient_name;
                $patient_mobile=$value->patient_mobile;
                $patient_age=$value->patient_age;
                $patient_visiting_purpose=$value->patient_visiting_purpose;
                $appointment_type=$value->appointment_type;
                
                $data_u = $this->db->select('image')->where(array('id'=>$value->patient_id))->get("users")->row();
                $patient_image = ($data_u && !empty($data_u->image)) ? base_url()."uploads/users/".$data_u->image : base_url()."uploads/profile-icon-3.png";
                
                $data1 = $this->db->where(array('id'=>$doctor_id))->get("doctors")->row();
                if ($data1) {
                    $doctor_name = $data1->doctor_name;
                    $hospital_name = $data1->hospital_name;
                    $address = $data1->address;
                    $doctor_image = base_url()."uploads/doctors/".$data1->doctor_image;
                    $designations_implode= $this->get_designation_names_csv($data1->designations); 
                    $blue_tick = $data1->blue_tick;
                    $doctor_rating = $data1->rating;
                    $total_users_reviewed = $data1->rating_count;
                    
                    $array[]= array(
                        'id'=>$value->id,
                        'doctor_name'=>$doctor_name,
                        'doctor_image'=>$doctor_image,
                        'designations'=>$designations_implode,
                        'hospital_name'=>$hospital_name,
                        'address'=>$address,
                        'date'=>$date_disp,
                        'time_slot_name'=>$time_slot_name,
                        'time_slot_value'=>$time_slot_value,
                        'patient_name'=>$patient_name,
                        'patient_mobile'=>$patient_mobile,
                        'patient_age'=>$patient_age,
                        'patient_visiting_purpose'=>$patient_visiting_purpose,
                        'consultation_fee'=>$consultation_fee,
                        'blue_tick'=>$blue_tick,
                        'doctor_rating'=>$doctor_rating,
                        'total_users_reviewed'=>$total_users_reviewed,
                        'doctor_status'=>$value->doctor_status,
                        'appointment_type'=>$appointment_type,
                        'patient_image'=>$patient_image,
                        'source' => isset($value->source) ? $value->source : 'offline'
                    ); 
                    $total_consultation_fee += (float)$consultation_fee;
                }
            }
           
            if(count($array)>0)
            {  
                return array('status' =>TRUE,'data'=>$array,'total_consultation_fee'=>(string)$total_consultation_fee);
            }
        }
        
        return array('status'=>FALSE,'message'=>"No data found");
    }

    function getDesignation()
    {
                    $this->db->select("id,name");
        $doctors = $this->db->where(array('status'=>1))->get('designations')->result();
            if(count($doctors)>0)
            {
                $ar = array('status'=>TRUE,'data'=>$doctors);
                return $ar;
            }
            else
            {
                $ar = array('status'=>FALSE,'message'=>"No data found");
                return $ar;
            }
    }
    
    
    function send_message($message = "", $mobile_number) {


            $message = urlencode($message);

            $url = "http://prioritysms.tulsitainfotech.com/api/mt/SendSMS?user=a3advertising&password=A3@90442&senderid=SRGLKO&channel=Trans&DCS=0&flashsms=0&number=".$mobile_number."&text=".$message."&route=15";

            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $send = curl_exec($ch);
            curl_close($ch);
            //print_r($send); die;

         return true;
      }


      function getSpecialisation()
      {
        $data = $this->db->where("status","active")->get("specialisation")->result();
        if(!empty($data))
        {
          return array("status"=>TRUE,"data"=>$data);
        }
        else
        {
           return array("status"=>FALSE,"message"=>"No data found");
        }
      }

      function getSpecialistIn($specialisation_id)
      {
                $this->db->select("id,specialist_in");
                $this->db->where("specialisation_id",$specialisation_id);
                $this->db->where("status","active");
        $data = $this->db->get("specialist_in")->result();
        if(!empty($data))
        {
          return array("status"=>TRUE,"data"=>$data);
        }
        else
        {
           return array("status"=>FALSE,"message"=>"No data found");
        }
      }


      function versionControl()
    {
        $qry = $this->db->query("select * from version_control where id=1");
       
        return array('status'=>TRUE,'android_doctor_version'=>"1.0.0",'ios_doctor_version'=>"1.0.0");
        
    }


}


