<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_reports extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $data['page_name'] = 'user_reports';
       
        $qry=$this->db->query("select * from user_reports");
        $data['user_report']=$qry->result();

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/user_reports', $data);
        $this->load->view('admin/includes/footer');

    }

    function complete($id)
    {
        $upd = $this->db->update('user_reports',array('status'=>1),array('id'=>$id));
        if($upd)
        {
            $this->session->set_flashdata('success_message', 'User report completed');
                 redirect('admin/user_reports');
                    die();
        }
    }

     



}

