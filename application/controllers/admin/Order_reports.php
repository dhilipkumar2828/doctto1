<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Order_reports extends MY_Controller {

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
        $data['page_name'] = 'todayorders';
       $data['title'] = 'Today Orders';
        $cdate=date('Y-m-d'); 
        $qry = $this->db->query("select * from orders where created_date LIKE '%".$cdate."%' and order_status=5 and bid_id=0");
        $data['orders'] = $qry->result();
        
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/order_report', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function allreports()
    {
        $data['page_name'] = 'allorders';
        $data['title'] = 'Total Orders';
        $cdate=date('Y-m-d'); 
        $qry = $this->db->query("select * from orders where order_status=5");
        $data['orders'] = $qry->result();
        
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/order_report', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function datewiseReport()
    {
        $start_date = $this->input->get_post('start_date');
        $end_date = $this->input->get_post('end_date');

        $data['start_date']=$start_date;
        $data['end_date']=$end_date;
        $data['title'] = 'Date wise Orders';
        $cdate=date('Y-m-d'); 
        $qry = $this->db->query("select * from orders where order_status=5 and created_date<='".$end_date."' and created_date>='".$start_date."'");
        $data['orders'] = $qry->result();
        
        $this->load->view('admin/includes/header', $data);
        $this->load->view('admin/order_report', $this->data);
        $this->load->view('admin/includes/footer');
    }

   



    


}
