<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cities extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $this->data['page_name'] = 'cities';
        $this->data['title'] = 'Cities';
        $this->data['show'] = 'true';
        $qry = $this->db->query("select * from cities");
        $this->data['cities'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/cities', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function add() {
        //$this->data['shop_id'] = $shop_id;
        $this->data['title'] = 'Add City';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_city', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function edit($id) {
        $qry = $this->db->query("select * from cities where id='".$id."'");
        $this->data['city_row'] = $qry->row();
        $this->data['title'] = 'Edit City';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/edit_city', $this->data);
        $this->load->view('admin/includes/footer');
    }

    function insert() {
       // $shop_id = $this->input->get_post('shop_id');
        $state_id = $this->input->get_post('state_id');
        $city_name = $this->input->get_post('city_name');
        $data = array(
            'state_id' => $state_id,
            'city_name' => $city_name,
            'created_at' => time()
        );
        $insert_query = $this->db->insert('cities', $data);
        //echo $this->db->last_query(); die;
        if ($insert_query) {
            $this->session->set_flashdata('success_message', 'City Added Successfully');
            redirect('admin/cities');
            die();
        } else {
            redirect('admin/cities/add');
            die();
        }
    }

    function update() {
       // $shop_id = $this->input->get_post('shop_id');
        $id = $this->input->get_post('cid');
        $state_id = $this->input->get_post('state_id');
        $city_name = $this->input->get_post('city_name');
        $data = array(
            'state_id' => $state_id,
            'city_name' => $city_name,
            //'vendor_id' => $shop_id
        );
        $wr = array('id'=>$id);
        $insert_query = $this->db->update('cities', $data, $wr);
        if ($insert_query) {
            $this->session->set_flashdata('success_message', 'City Updated Successfully');
            redirect('admin/cities');
            die();
        } else {
            redirect('admin/cities/add');
            die();
        }
    }


    function delete($id) {
        $this->db->where('id', $id);
        if ($this->db->delete('cities')) {
                $this->session->set_flashdata('success_message', 'City Deleted Successfully');
                redirect('admin/cities');
         } 
         else 
         {
                $this->session->set_flashdata('error_message', 'Unable to delete');
                redirect('admin/cities');
         }

    }

   
}
