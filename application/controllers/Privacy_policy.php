<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privacy_policy extends CI_Controller {

     private $data; 
        function __construct() {  
            parent::__construct();
    //        if ($this->session->userdata('logged_in') != true) {
    //            //$this->session->set_flashdata('error', 'Session Timed Out');
    //            redirect('admin/login');
    //        }      
        } 
    public function index()
    {   
                $this->data['title'] = 'Welcome';
        $this->load->view('privacypolicy', $this->data);
    }
}
