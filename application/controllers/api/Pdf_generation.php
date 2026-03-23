<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_generation extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        
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
