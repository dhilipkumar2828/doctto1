<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctors_appointments extends MY_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            redirect('admin/login');
        }

        $this->load->model('admin/doctors_appointment_model');
        $this->load->model("admin_model");
        $this->data['page_name'] = 'Doctors Appoointment';

    }

    function index() {
        $this->data['page_title'] = 'Doctors Appointment';
        $this->data['page_name'] = 'doctors_appointment';
        
        $appointment = $this->doctors_appointment_model->get_appointment();
        
        $taday_date = date('Y-m-d');
        $tomorrow_date = date('Y-m-d',strtotime('+1 days'));
        $this->data['tomorrow'] = $this->db->where(array('date>'=>$taday_date,'date<='=>$tomorrow_date))->get('doctor_appointments')->num_rows();
        
          $this->data['today'] = $this->db->where(array('date'=>$taday_date))->get('doctor_appointments')->num_rows();

        $this->data['appointment']=$appointment;
        $this->admin_view('doctors_appointments');
    }
    
    
      function doctor_payments() {
          
        $this->data['page_title'] = 'Doctors Payments';
    
        $appointment = $this->doctors_appointment_model->get_appointment();

        $this->data['appointment']=$appointment;
        $this->admin_view('doctors_payments');
    }
    
  function searchorderdate(){
        $start_date= $this->input->post("start_date");
        $end_date= $this->input->post("end_date");
       
        $this->data['start_date']=$start_date;
        $this->data['end_date']=$end_date;
       
        $this->data['appointment'] = $this->doctors_appointment_model->search($start_date,$end_date);
         
            $this->admin_view('doctors_appointments');
    }

    function add() {
        //print_r($_FILES);exit;
        $this->data['page_title'] = 'Add Doctors Appointment';
        $this->admin_view('add_doctors_appointment');
    }
    
       function appointment_status() { 
        $this->data['page_title'] = 'Doctors Appointment';
        $status = $this->input->get('status');
  
                if($status=='active'){
                 $doc_app_status = $this->db->where('doctor_status','active')->get('doctor_appointments')->result();
                }
                elseif ($status=='accept') {
                    $doc_app_status = $this->db->where('doctor_status','accept')->get('doctor_appointments')->result();
                }
                elseif ($status=='completed') {
                    $doc_app_status = $this->db->where('doctor_status','completed')->get('doctor_appointments')->result();
                    //echo $this->db->last_query(); die;
                }
                elseif ($status=='reject') {
                   $doc_app_status = $this->db->where('doctor_status','reject')->get('doctor_appointments')->result();
                }  
        $this->data['appointment'] = $doc_app_status;   
        /*$this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/appointment_status', $this->data);
        $this->load->view('admin/includes/footer');*/

        $this->admin_view('doctors_appointments');
}
    function tomorrow_appointment() { 
        $this->data['page_title'] = 'Doctors Appointment';
         $taday_date = date('Y-m-d');
        $tomorrow_date = date('Y-m-d',strtotime('+1 days'));
        $tomorrow = $this->db->where(array('date>'=>$taday_date,'date<='=>$tomorrow_date))->get('doctor_appointments')->result();
        $this->data['tomorrow_data'] = $tomorrow;    
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/tomorrow_appointment', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function manage_doctor_symptoms($doctor_id) {
        if (!$doctor_id) {
            redirect('admin/doctors/');
            die();
        }

        $this->data['doctor_id'] = $doctor_id;

        //$this->data['shop_status'] = 'add';

        $this->data['doctor_data'] = $this->doctor_manage_category_model->get_doctor_symptoms($doctor_id);
        $this->data['symptom'] = $this->doctor_manage_category_model->getSymptoms();
        $this->data['doctor_data_symptom_ids'] = array_column($this->data['doctor_data'], 'symptom_id');

        $this->data['title'] = 'Manage Doctor Categories';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/doctor_manage_symptoms', $this->data);

        $this->load->view('admin/includes/footer');
    }
      
     function eprescription($appointment_id) {
       
            if (!$appointment_id) {
            redirect('admin/doctors_appointments/');
            die();
        }
          $data['appointment_id'] = $appointment_id;
        //   print_r($data['appointment_id']);die;

                                             $this->db->where('appointment_id',$data['appointment_id']);
          $data['patient_prescription_id'] = $this->db->get('patient_prescription')->row();
        

                                   $this->db->where('patient_prescription_id',$data['patient_prescription_id']->id);
          $data['eprescription'] = $this->db->get('eprescription')->result();
          
          
        //   print_R( $data['eprescription']);die;
         
                                                    $this->db->where('appointment_id',$data['appointment_id']);
                                                    $this->db->where('prescription_type','prescription');
          $data['manual_prescription'] = $this->db->get('patient_prescription')->row();
          
                                                    $this->db->where('appointment_id',$data['appointment_id']);
                                                    $this->db->where('prescription_type','diagnosis');
          $data['diagnosis'] = $this->db->get('patient_prescription')->row();
          
        //   print_r($data['diagnosis']);die;
          
        //   echo $this->db->last_query();die;
          
         $this->db->where('patient_prescription_id',$data['patient_prescription_id']->id);
          $data['lab_tests'] = $this->db->get('lab_tests')->result();  
          
         $data['val_id'] = $appointment_id;

        $this->data['title'] = 'Prescription';
        //echo "<pre>"; print_r($this->data); die;
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/prescription', $data);
        $this->load->view('admin/includes/footer');
    }
    
 
 
 function view_pdf($appointment_id) {
        
        
                             $this->db->select('doctor_id');
                             $this->db->where('id',$appointment_id);
    $this->data['doctor_id'] = $this->db->get('doctor_appointments')->row();    
    

    
                             
                             $this->db->where('id',$this->data['doctor_id']->doctor_id);
    $this->data['doc_det'] = $this->db->get('doctors')->row();  
    
    // echo $this->db->last_query();die;
    
                          $this->db->where('id',$this->data['doc_det']->designations);
    $this->data['desg'] = $this->db->get('designations')->row()->name; 
    
                          $this->db->where('id',$this->data['doc_det']->specialisation);
    $this->data['spcl'] = $this->db->get('specialisation')->row()->name;   
    
    // print_r($this->data['desg']);die;  
    
    // print_r($this->data['doc_det']);die;
    
                             $this->db->where('id',$appointment_id);
    $this->data['patient'] = $this->db->get('doctor_appointments')->row();
    

    
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','diagnosis');
    $this->data['diag'] = $this->db->get('patient_prescription')->row();
  
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','prescription');
    $this->data['pres'] = $this->db->get('patient_prescription')->row();
    
                            $this->db->select('id');
                            $this->db->where('appointment_id',$appointment_id);
    $this->data['epresp'] = $this->db->get('patient_prescription')->row(); 
    
  
                          
                                   $this->db->where('patient_prescription_id',$this->data['epresp']->id);
    $this->data['eprescription'] = $this->db->get('eprescription')->result();

        $base_path = str_replace("system", "vendor", BASEPATH);

        require_once $base_path . '/autoload.php';


        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 50,
            'margin_bottom' => 10
        ]);

        $this->data["mpdf"] = $mpdf;

        $mpdf->SetWatermarkImage(
                base_url() , 0.05, ''
        );
        $mpdf->showWatermarkImage = true;
        $mpdf->use_kwt = true;
        $mpdf->autoPageBreak = true;

        //$html = file_get_contents($_GET['url']);
        //$html = $this->load->view("pos_agreement/index", $this->data, true);
        $html = $this->load->view("invoice", $this->data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output(); 
    }
    
    //  function labtest_pdf($appointment_id) {
        
        
    //                          $this->db->select('doctor_id');
    //                          $this->db->where('id',$appointment_id);
    // $this->data['doctor_id'] = $this->db->get('doctor_appointments')->row();    
    
                             
    //                          $this->db->where('id',$this->data['doctor_id']->doctor_id);
    // $this->data['doc_det'] = $this->db->get('doctors')->row();  
    
    //                       $this->db->where('id',$this->data['doc_det']->designations);
    // $this->data['desg'] = $this->db->get('designations')->row()->name; 
    
    //                       $this->db->where('id',$this->data['doc_det']->specialisation);
    // $this->data['spcl'] = $this->db->get('specialisation')->row()->name;   
    
    // // print_r($this->data['desg']);die;  
    
    // // print_r($this->data['doc_det']);die;
    
    //                          $this->db->where('id',$appointment_id);
    // $this->data['patient'] = $this->db->get('doctor_appointments')->row();
    

    
    //                           $this->db->where('appointment_id',$appointment_id);
    //                           $this->db->where('prescription_type','diagnosis');
    // $this->data['diag'] = $this->db->get('patient_prescription')->row();
  
    //                           $this->db->where('appointment_id',$appointment_id);
    //                           $this->db->where('prescription_type','prescription');
    // $this->data['pres'] = $this->db->get('patient_prescription')->row();
    
    //                         $this->db->select('id');
    //                         $this->db->where('appointment_id',$appointment_id);
    // $this->data['epresp'] = $this->db->get('patient_prescription')->row(); 
    
  
                          
    //                               $this->db->where('patient_prescription_id',$this->data['epresp']->id);
    // $this->data['eprescription'] = $this->db->get('eprescription')->result();

    //     $base_path = str_replace("system", "vendor", BASEPATH);

    //     require_once $base_path . '/autoload.php';


    //     $mpdf = new \Mpdf\Mpdf([
    //         'mode' => 'utf-8',
    //         'format' => 'A4',
    //         'margin_left' => 20,
    //         'margin_right' => 20,
    //         'margin_top' => 50,
    //         'margin_bottom' => 10
    //     ]);

    //     $this->data["mpdf"] = $mpdf;

    //     $mpdf->SetWatermarkImage(
    //             base_url() , 0.05, ''
    //     );
    //     $mpdf->showWatermarkImage = true;
    //     $mpdf->use_kwt = true;
    //     $mpdf->autoPageBreak = true;

    //     //$html = file_get_contents($_GET['url']);
    //     //$html = $this->load->view("pos_agreement/index", $this->data, true);
    //     $html = $this->load->view("labtest_invoice", $this->data, true); 
    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output(); 
    // }
    
    
      function labtest_pdf($appointment_id) {
          
          
          
                   $this->db->select('doctor_id');
                             $this->db->where('id',$appointment_id);
    $this->data['doctor_id'] = $this->db->get('doctor_appointments')->row();    
    
                             
                             $this->db->where('id',$this->data['doctor_id']->doctor_id);
    $this->data['doc_det'] = $this->db->get('doctors')->row();  
    
                          $this->db->where('id',$this->data['doc_det']->designations);
    $this->data['desg'] = $this->db->get('designations')->row()->name; 
    
                          $this->db->where('id',$this->data['doc_det']->specialisation);
    $this->data['spcl'] = $this->db->get('specialisation')->row()->name;   
        
        
    //                          $this->db->select('doctor_id');
    //                          $this->db->where('id',$appointment_id);
    // $this->data['doctor_id'] = $this->db->get('doctor_appointments')->row();    
    
                             
    //                          $this->db->where('id',$this->data['doctor_id']->doctor_id);
    // $this->data['doc_det'] = $this->db->get('doctors')->row();  
    
    // print_r($this->data['doc_det']);die;
    
                             $this->db->where('id',$appointment_id);
    $this->data['patient'] = $this->db->get('doctor_appointments')->row();
    

    
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','diagnosis');
    $this->data['diag'] = $this->db->get('patient_prescription')->row();
  
                               $this->db->where('appointment_id',$appointment_id);
                               $this->db->where('prescription_type','prescription');
    $this->data['pres'] = $this->db->get('patient_prescription')->row();
    
                            $this->db->select('id');
                            $this->db->where('appointment_id',$appointment_id);
    $this->data['epresp'] = $this->db->get('patient_prescription')->row(); 
    
  
                          
                                   $this->db->where('patient_prescription_id',$this->data['epresp']->id);
    $this->data['labtest'] = $this->db->get('lab_tests')->result();

        $base_path = str_replace("system", "vendor", BASEPATH);

        require_once $base_path . '/autoload.php';


        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 50,
            'margin_bottom' => 10
        ]);

        $this->data["mpdf"] = $mpdf;

        $mpdf->SetWatermarkImage(
                base_url() , 0.05, ''
        );
        $mpdf->showWatermarkImage = true;
        $mpdf->use_kwt = true;
        $mpdf->autoPageBreak = true;

        //$html = file_get_contents($_GET['url']);
        //$html = $this->load->view("pos_agreement/index", $this->data, true);
        $html = $this->load->view("labtest_invoice", $this->data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

 

    }


