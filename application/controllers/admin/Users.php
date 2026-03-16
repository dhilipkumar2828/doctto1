<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $data['page_name'] = 'users';
        $qry=$this->db->query("select * from users order by id desc");
        $data['users']=$qry->result();

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/users', $data);
        $this->load->view('admin/includes/footer');

    }


function inactiveusers() {
        $data['page_name'] = 'users';
        $qry=$this->db->query("select * from users where otp_status=0");
        $data['users']=$qry->result();

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/users', $data);
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

    function view($user_id)
    {
        $data['page_name'] = 'users';
        $qry= $this->db->where('id',$user_id)->get('users');
        $data['users']=$qry->row(); 
        
        $data['appointment'] = $this->db->where('patient_mobile',$data['users']->phone)->get('doctor_appointments')->result(); 

        // print_R($appointment);die; 

        $this->load->view('admin/includes/header', $data);

        $this->load->view('admin/viewusers', $data);
        $this->load->view('admin/includes/footer');
    }



}

