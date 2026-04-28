<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_terms extends MY_Controller {
    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            redirect('admin/login');
        }
    }

    function index() {
        $this->data['page_name'] = 'subscription_terms';
        $this->data['title'] = 'Subscription Terms';
        
        $this->data['content'] = $this->db->get('subscription_terms')->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/subscription_terms/subscription_terms', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function add() {
        $this->data['page_name'] = 'subscription_terms';
        $this->data['title'] = 'Add Subscription Terms';

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/subscription_terms/add_subscription_terms', $this->data);
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

        $insert_query = $this->db->insert('subscription_terms', $data);

        if ($insert_query) {
            $this->session->set_flashdata('success_message', 'Subscription Terms Added Successfully.');
            redirect('admin/subscription_terms');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/subscription_terms/add');
            die();
        }
    }

    function edit($id) {
        $this->data['page_name'] = 'subscription_terms';
        $this->data['title'] = 'Edit Subscription Terms';

        $this->data['content'] = $this->db->get_where('subscription_terms', ['id' => $id])->row();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/subscription_terms/edit_subscription_terms', $this->data);
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
        $wr = array('id' => $id);
        $update_query = $this->db->update('subscription_terms', $data, $wr);
        if ($update_query) {
            $this->session->set_flashdata('success_message', 'Subscription Terms updated Successfully.');
            redirect('admin/subscription_terms');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/subscription_terms/edit/' . $id);
            die();
        }
    }

    function delete($id) {
        $wr = array('id' => $id);
        $del = $this->db->delete("subscription_terms", $wr);
        if ($del) {
            $this->session->set_flashdata('error_message', 'Subscription Terms deleted Successfully.');
            redirect('admin/subscription_terms');
            die();
        } else {
            $this->session->set_flashdata('error_message', 'Something went wrong, Please try again.');
            redirect('admin/subscription_terms');
            die();
        }
    }
}
