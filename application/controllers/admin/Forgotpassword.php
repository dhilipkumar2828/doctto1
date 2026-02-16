<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgotpassword extends CI_Controller {
    private $data;
    function __construct() {
        parent::__construct();        
        $this->load->model("admin_model");
//        if ($this->session->userdata('logged_in') == true) {
//            redirect('admin/dashboard');
//        }
//        $this->data['site_details'] = $this->admin_model->get_row_by_id('1', 'profile');
    }

    public function index() {   
//        $this->data['username'] = 'admin';
//        $this->data['password'] = 'Wido@5454';
        $this->load->view('admin/forgotpassword', $this->data);
    }

    public function sendEmail() {
           
        
            $username = $this->input->get_post('email', TRUE);
            $check_email_qry = $this->db->query("select * from admin where email='".$username."'");
            $check_email_row = $check_email_qry->row();
                $otp = rand(1000,10000);

            if ($check_email_qry->num_rows()>0)
            {
                 $from_email=$this->input->post('email');
                
                 $to_mail ='ankadisatish1919@gmail.com';
                 $from_email = 'ankadisatish1919@gmail.com';

                 $this->session->set_flashdata('error_message', "sendt successfully");
                 redirect('admin/forgotpassword');
            } 
            else 
            {

                $this->session->set_flashdata('error_message', 'Invalid Email ID ');
                 redirect('admin/forgotpassword');

               
                $this->session->set_flashdata('msg', 'Welcome');
                $this->session->set_userdata('admin_login', $sess_arr);

                redirect('admin/dashboard');
            }

    }

}
