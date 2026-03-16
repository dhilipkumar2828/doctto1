<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $data['page_name'] = 'transactions';
        $qry=$this->db->query("select * from transactions");
        $data['transactions']=$qry->result();

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/transactions', $data);
        $this->load->view('admin/includes/footer');

    }

     function delete($user_id) {
        $this->db->where('id', $user_id);
       $del = $this->db->delete('users');

        //echo$del = $this->db->last_query(); die;
        if($del)
        {
            $this->session->set_flashdata('success_message', 'User Deleted Successfully');
            redirect('admin/users');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, Unable to delete');
            redirect('admin/users');
        }
    }



}

