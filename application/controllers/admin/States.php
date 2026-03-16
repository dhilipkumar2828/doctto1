<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class States extends MY_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) {

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }

    }



    function index() {
        $this->data['page_name'] = 'states';

        $this->data['title'] = 'States';
        $qry = $this->db->query("select * from states");
        $this->data['states'] = $qry->result();

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/states', $this->data);

        $this->load->view('admin/includes/footer');

    }



   function add() 
   {
        $this->data['title'] = 'Add State';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_state', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function insert() {
        $state_name = $this->input->get_post('state_name');

        $chk = $this->db->query("select * from states where state_name='".$state_name."'");
        if($chk->num_rows()>0)
        {
                 $this->session->set_flashdata('error_message', 'State already Exist');
                 redirect('admin/states/add');
                    die();
        }
        else
        {
                 $data = array(
            'state_name' => $state_name,
            'country_id' =>1,
            'created_at' => time()
        );
        $insert_query = $this->db->insert('states', $data);

        if ($insert_query) {
             $this->session->set_flashdata('success_message', 'State added Successfully');
            redirect('admin/states');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something Went wrong');
                 redirect('admin/states/add');
                    die();
        }
        }

       
    }

    function update() {
        $id = $this->input->get_post('sid');
        $state_name = $this->input->get_post('state_name');
        $chk = $this->db->query("select * from states where id!='".$id."' and state_name='".$state_name."'");
        if($chk->num_rows()>0)
        {
                 $this->session->set_flashdata('error_message', 'State already Exist');
                 redirect('admin/states/add');
                    die();
        }
        else
        {

            $wr = array('id'=>$id);
            $data = array(
                'state_name' => $state_name,
                'country_id' =>1,
                'created_at' => time()
            );
            $insert_query = $this->db->update('states', $data, $wr);
            if ($insert_query) {
                redirect('admin/states');
                die();
            } else {
                redirect('admin/states/edit');
                die();
            }
        }
    }



    function edit($id) 
    {
        $qry = $this->db->query("select * from states where id='".$id."'");
        $this->data['states'] = $qry->row();
        $this->data['title'] = 'Edit State';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/edit_state', $this->data);
       $this->load->view('admin/includes/footer');
    }

     function delete($id) {
        $this->db->where('id', $id);
        if ($this->db->delete('states')) {

            $this->db->delete('cities',array('state_id' =>$id));
                $this->session->set_flashdata('success_message', 'State Deleted Successfully');
                redirect('admin/states');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/states');
         }

    }

}

