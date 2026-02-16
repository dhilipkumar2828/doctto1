<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

    public function __construct() {

        parent:: __construct();

        $this->load->helper('url');
    }

    public function index() {
    	 $this->session->unset_userdata('admin_login');
        //$this->session->unset_userdata('admin');
        $this->session->set_flashdata('success_message', 'Successfully logged out');        
        redirect('admin/login');
    }

}
