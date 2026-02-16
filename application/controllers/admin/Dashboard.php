<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        //echo $this->session->userdata(); 
        
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
        $this->load->model("admin_model");
    }

    function index() {
        $this->data['title'] = 'Dashboard';
      
$this->data['page_name'] = 'dashboard';
        $this->data['active_total_users'] = $this->db->get('users')->num_rows();


        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/home', $this->data);
        $this->load->view('admin/includes/footer');
    }

}
