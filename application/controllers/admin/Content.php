<?php



defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends CI_Controller {
    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }

    }



    function index() {
        $this->data['page_name'] = 'content';
        $this->data['title'] = 'Content';
        
        $this->data['content'] = $this->db->get('content')->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/content', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function add() {
         $this->data['page_name'] = 'content';
        $this->data['title'] = 'Add Content';

        $this->load->view('admin/includes/header', $this->data);

        $this->load->view('admin/add_content', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function insert() {

        $title = $this->input->get_post('title');
        $description = $this->input->get_post('description');
        $status = $this->input->get_post('status');

        $data = array(
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'created_date' => time()

        );

        $insert_query = $this->db->insert('content', $data);

        if ($insert_query) {
            $this->session->set_flashdata('success_message', 'Page Added Successfully.');
            redirect('admin/content');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/content/add');
            die();
        }

    }



    function edit($id) {

        $this->data['title'] = 'Edit Content';

        $this->data['content'] = $this->db->get_where('content', ['id' => $id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_content', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() {
        $id = $this->input->get_post('id');
        $title = $this->input->get_post('title');
        $description = $this->input->get_post('description');
        $status = $this->input->get_post('status');

        $data = array(
            'title' => $title,
            'description' => $description,
            'status' => $status
        );
        $wr = array('id'=>$id);
        $insert_query = $this->db->update('content', $data,$wr);
        if($insert_query) 
        {
            $this->session->set_flashdata('success_message', 'Content Page updated Successfully.');
            redirect('admin/content');
            die();
        }
        else 
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/content/edit');
            die();
        }

    }



    function delete($id)
    {
        $wr = array('id'=>$id);
        $del = $this->db->delete("content",$wr);
        if($del)
        {
            $this->session->set_flashdata('error_message', 'Content Page deleted Successfully.');
            redirect('admin/content');
            die(); 
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/content');
            die(); 
        }
        
    }



}

