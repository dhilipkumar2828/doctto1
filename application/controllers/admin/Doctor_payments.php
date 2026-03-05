<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctor_payments extends MY_Controller {
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
        $this->data['page_title'] = 'Doctors Payments';
        $this->data['page_name'] = 'doctor_payments';
        $appointment = $this->doctors_appointment_model->get_appointment();
        
        $taday_date = date('Y-m-d');
        $tomorrow_date = date('Y-m-d',strtotime('+1 days'));
        $this->data['tomorrow'] = $this->db->where(array('date>'=>$taday_date,'date<='=>$tomorrow_date))->get('doctor_appointments')->num_rows();
        
          $this->data['today'] = $this->db->where(array('date'=>$taday_date))->get('doctor_appointments')->num_rows();

        $this->data['appointment']=$appointment;
        $this->admin_view('doctor_payments'); 
    }
    
    
  function searchorderdate(){
        $start_date= $this->input->post("start_date");
        $end_date= $this->input->post("end_date");
       
        $this->data['start_date']=$start_date;
        $this->data['end_date']=$end_date;
       
        $this->data['appointment'] = $this->doctors_appointment_model->search($start_date,$end_date); 
         
            $this->admin_view('doctor_payments');
    }
    
    function view_invoice($appointment_id) {
        $source = $this->input->get('source');
        $table = ($source == 'online') ? 'online_doctor_appointments' : 'doctor_appointments';
        
        $this->data['tax_details'] = $this->db->get('tax_details')->row();   
        
        $this->db->where('id',$appointment_id);
        $this->data['doctor_id'] = $this->db->get($table)->row();    
        
        if (empty($this->data['doctor_id'])) {
            show_error('Appointment not found.');
            return;
        }
                                 
        $this->db->where('id',$this->data['doctor_id']->doctor_id);
        $this->data['doc_det'] = $this->db->get('doctors')->row();  
        
        if (!empty($this->data['doc_det'])) {
            $this->db->where('id',$this->data['doc_det']->designations);
            $desg_row = $this->db->get('designations')->row();
            $this->data['desg'] = !empty($desg_row) ? $desg_row->name : 'N/A';
        
            $this->db->where('id',$this->data['doc_det']->specialisation);
            $spcl_row = $this->db->get('specialisation')->row();
            $this->data['spcl'] = !empty($spcl_row) ? $spcl_row->name : 'N/A';
        } else {
            $this->data['desg'] = 'N/A';
            $this->data['spcl'] = 'N/A';
        }
        
        $this->db->where('id',$appointment_id);
        $this->data['patient'] = $this->db->get($table)->row();
        
        // Handling prescriptions (adjusting query if online might use different logic, but for now keeping same)
        $this->db->where('appointment_id',$appointment_id);
        $this->db->where('prescription_type','diagnosis');
        $this->data['diag'] = $this->db->get('patient_prescription')->row();
      
        $this->db->where('appointment_id',$appointment_id);
        $this->db->where('prescription_type','prescription');
        $this->data['pres'] = $this->db->get('patient_prescription')->row();
        
        $this->db->select('id');
        $this->db->where('appointment_id',$appointment_id);
        $this->data['epresp'] = $this->db->get('patient_prescription')->row(); 
    
  
                          
                                   $this->db->where('patient_prescription_id', !empty($this->data['epresp']) ? $this->data['epresp']->id : 0);
    $this->data['eprescription'] = $this->db->get('eprescription')->result();

        $base_path = str_replace("system", "vendor", BASEPATH);
        
        // print_R($base_path);die;

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
        $html = $this->load->view("payment_invoice", $this->data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output(); 
    }


 

 

    }


