<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_daily_reports extends MY_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
        $this->load->model("admin_model");
    }

    function index() {
        $this->data['page_name'] = 'vendors_shops';
        $this->data['title'] = 'Vendors/Shops';
        $this->data['start_date'] = '';
        $this->data['end_date'] = '';
                                                $this->db->order_by("id","desc");
        $this->data['appointment_commission'] = $this->db->where('doctor_status','completed')->get('doctor_appointments')->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/appointment_daily_reports', $this->data);

        $this->load->view('admin/includes/footer');
    }

    function datewiseReport()
    {
        $start_date = $this->input->get_post('start_date');
        //$start_date = date('Y-m-d', strtotime($start_date1. ' - 1 days'));
        $end_date1 = $this->input->get_post('end_date');
        $end_date = date('Y-m-d', strtotime($end_date1. ' + 0 days'));

        $this->data['start_date']=$start_date;
        $this->data['end_date']=$end_date1;
        $this->data['title'] = 'Date wise Orders';
        
        $where = "doctor_status='completed' and date BETWEEN '".$start_date."' AND '".$end_date."'";
        $report = $this->db->where($where)->get('doctor_appointments')->result();
        //echo $this->db->last_query(); die;
        $this->data['appointment_commission'] = $report;
        
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/appointment_daily_reports', $this->data);
        $this->load->view('admin/includes/footer');
    }







}

