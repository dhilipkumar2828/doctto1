<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dip extends CI_Controller {

    function __construct() {

        //load the parent constructor

        parent::__construct();
        $this->load->model("admin_model");
    }

    public function index() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date('d-m-Y h:i:s');
        $ip_insert = $this->admin_model->ip_insert($ip, $date);
        $this->session->set_flashdata('msg', 'Now you have right to access admin panel');
        redirect('admin/login');
    }

}
