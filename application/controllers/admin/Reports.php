<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Reports extends MY_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            redirect('admin/login');
        }

        $this->load->model('admin/doctors_appointment_model');
        $this->load->model("admin_model");
        $this->data['page_name'] = 'Reports';

    } 
 
    function index() {
        $this->data['page_title'] = 'Reports';
    
        $appointment = $this->doctors_appointment_model->get_appointment();
        
        $taday_date = date('Y-m-d');
        $tomorrow_date = date('Y-m-d',strtotime('+1 days'));
        $this->data['tomorrow'] = $this->db->where(array('date>'=>$taday_date,'date<='=>$tomorrow_date))->get('doctor_appointments')->num_rows();
        
          $this->data['today'] = $this->db->where(array('date'=>$taday_date))->get('doctor_appointments')->num_rows();

        $this->data['appointment']=$appointment;
        $this->admin_view('reports');  
    }
    
    
  function searchorderdate(){
        $start_date= $this->input->post("start_date");
        $end_date= $this->input->post("end_date");
       
        $this->data['start_date']=$start_date;
        $this->data['end_date']=$end_date;
       
        $this->data['appointment'] = $this->doctors_appointment_model->search($start_date,$end_date);
         
            $this->admin_view('doctor_payments');
    }

 

 

    }


