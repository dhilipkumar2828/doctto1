<?php

defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class MY_Controller extends CI_Controller {
    public $data;
     function __construct() {
        parent::__construct();
        $this->data = array();
       
        
    }

    public function my_view($design_view) {
//        $this->load->view("includes/header", $this->data);
        $this->load->view($design_view);
//        $this->load->view("includes/footer");
    }
     function admin_view($design_view) {
       $this->load->view("admin/includes/header", $this->data);
//        $this->load->view("admin/menu", $this->data);
        $this->load->view("admin/" . $design_view);
      $this->load->view("admin/includes/footer");
    }

    
}