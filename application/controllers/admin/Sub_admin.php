<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Sub_admin extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {

        $this->data['page_name'] = 'sub_admin';

        $this->data['title'] = 'Sub Admin';
        $qry = $this->db->query("select * from sub_admin");
        $this->data['subadmins'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/sub_admins', $this->data);

        $this->load->view('admin/includes/footer');

    }



   function add() 
   {
        $this->data['title'] = 'Add Sub Admin';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_subadmin', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function insert() {

        if(count($this->input->get_post('user_permission'))==0)
        {
             $this->session->set_flashdata('error_message', 'Select Minimum One User Permission');
                 redirect('admin/sub_admin/add');
                    die();
        }
        $username = $this->input->get_post('username');
        $password = $this->input->get_post('password');
        $login_status = $this->input->get_post('login_status');
        $user_permission = implode(",",$this->input->get_post('user_permission'));

        $chk = $this->db->query("select * from sub_admin where username='".$username."'");
        if($chk->num_rows()>0)
        {
                 $this->session->set_flashdata('error_message', 'Email already Exist');
                 redirect('admin/sub_admin/add');
                    die();
        }
        else
        {
            $data = array(
            'username' => $username,
            'password' =>md5($password),
            'login_status' =>$login_status,
            'permissions' => $user_permission
        );
        $insert_query = $this->db->insert('sub_admin', $data);
        if ($insert_query) {
             $this->session->set_flashdata('success_message', 'Sub Admin added Successfully');
            redirect('admin/sub_admin');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something Went wrong');
                 redirect('admin/sub_admin/add');
                    die();
        }
        }

       
    }

    function update() {
        $admin_id = $this->input->get_post('admin_id');
        $username = $this->input->get_post('username');
        $password = $this->input->get_post('password');
        $login_status = $this->input->get_post('login_status');
        $user_permission = implode(",",$this->input->get_post('user_permission'));

       
            $data = array(
            'username' => $username,
            'password' =>sha1($password),
            'login_status' =>$login_status,
            'permissions' => $user_permission
        );
            $wr = array('id'=>$admin_id);
        $insert_query = $this->db->update('sub_admin', $data,$wr);
        if ($insert_query) {
             $this->session->set_flashdata('success_message', 'Sub Admin updated Successfully');
            redirect('admin/sub_admin');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something Went wrong');
                 redirect('admin/sub_admin/edit');
                    die();
        }
       
    }



    function edit($id) 
    {
        $qry = $this->db->query("select * from sub_admin where id='".$id."'");
        $this->data['sub_admin_row_data'] = $qry->row();
        $this->data['title'] = 'Edit Sub Admin';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/edit_subadmin', $this->data);
       $this->load->view('admin/includes/footer');
    }

     function delete($id) {
        $this->db->where('id', $id);
        if ($this->db->delete('sub_admin')) {

           
                $this->session->set_flashdata('success_message', 'Sub Admin Deleted Successfully');
                redirect('admin/sub_admin');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/sub_admin');
         }

    }

}

