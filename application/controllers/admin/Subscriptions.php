<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            redirect('admin/login');
        }
        $this->load->model('admin_model');
    }

    public function index() {
        $this->data['page_name'] = 'subscriptions';
        $this->data['title'] = 'Subscriptions Management';
        
        $type = $this->input->get('type') ? $this->input->get('type') : 'customer';
        $this->data['selected_type'] = $type;

        $this->db->where('plan_type', $type);
        $this->data['plans'] = $this->db->get('subscription_plans')->result();

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/subscriptions/manage', $this->data);
        $this->load->view('admin/includes/footer');
    }

    public function create() {
        $type = $this->input->post('type');
        // In create()
        $data = array(
            'name' => $this->input->post('name'),
            'plan_type' => $type,
            'price' => $this->input->post('price'),
            'duration_days' => $this->input->post('duration_days') ? $this->input->post('duration_days') : 30,
            'call_chat' => $this->input->post('call_chat') ? $this->input->post('call_chat') : NULL,
            'whatsapp_chat' => $this->input->post('whatsapp_chat') ? $this->input->post('whatsapp_chat') : NULL,
            'description' => $this->input->post('description'),
            'perks' => $this->input->post('perks'),
            'max_doctors_allowed' => $this->input->post('max_doctors_allowed') ? $this->input->post('max_doctors_allowed') : 0,
            'total_consultations' => 0,
            'is_active' => $this->input->post('is_active')
        );

        $this->db->insert('subscription_plans', $data);

        $this->session->set_tempdata('success', 'New plan created successfully', 1);
        redirect('admin/subscriptions?type=' . $type);
    }

    public function delete($id) {
        $type = $this->input->get('type');
        $this->db->where('id', $id)->delete('subscription_plans');
        $this->session->set_tempdata('success', 'Plan deleted successfully', 1);
        redirect('admin/subscriptions?type=' . $type);
    }

    public function update() {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        
        $data = array(
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'description' => $this->input->post('description'),
            'perks' => $this->input->post('perks'),
            'duration_days' => $this->input->post('duration_days'),
            'call_chat' => $this->input->post('call_chat') ? $this->input->post('call_chat') : NULL,
            'whatsapp_chat' => $this->input->post('whatsapp_chat') ? $this->input->post('whatsapp_chat') : NULL,
            'max_doctors_allowed' => $this->input->post('max_doctors_allowed') ? $this->input->post('max_doctors_allowed') : 0,
            'is_active' => $this->input->post('is_active')
        );

        $this->db->where('id', $id)->update('subscription_plans', $data);

        $this->session->set_tempdata('success', 'Plan updated successfully', 1);
        redirect('admin/subscriptions?type=' . $type);
    }
}
