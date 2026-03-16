<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_subscription_plans extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/doctor_subscription_plans_model');
        $this->load->model('admin/doctors_model');
        $this->load->model('admin_model');
        $this->data['page_name'] = 'doctor_subscription_plans';
    }

    function index() {
        $this->data['page_title'] = 'Doctor Subscription Plans';
        $plans = $this->doctor_subscription_plans_model->get_all_plans();
        $this->data['plans'] = $plans;
        $this->admin_view('doctor_subscription_plans');
    }

    function add() {
        $this->data['page_title'] = 'Add Doctor Subscription Plan';
        $this->admin_view('add_doctor_subscription_plan');
    }

    function insert() {
        $this->form_validation->set_rules('name', 'Plan Name', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('duration_days', 'Duration Days', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('perks', 'Perks/Benefits', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_subscription_plans/add');
        }

        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'price' => $this->input->post('price'),
            'duration_days' => $this->input->post('duration_days'),
            'perks' => $this->input->post('perks'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->doctor_subscription_plans_model->insert_plan($data)) {
            $this->session->set_flashdata('success_message', 'Doctor Subscription Plan Added Successfully');
            redirect('admin/doctor_subscription_plans');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add doctor subscription plan');
            redirect('admin/doctor_subscription_plans/add');
        }
    }

    function edit($id) {
        if (!$id) {
            redirect('admin/doctor_subscription_plans');
        }

        $this->data['page_title'] = 'Edit Doctor Subscription Plan';
        $this->data['plan'] = $this->doctor_subscription_plans_model->get_plan_by_id($id);
        
        if (!$this->data['plan']) {
            $this->session->set_flashdata('error_message', 'Plan not found');
            redirect('admin/doctor_subscription_plans');
        }
        
        $this->admin_view('edit_doctor_subscription_plan');
    }

    function update() {
        $plan_id = $this->input->post('plan_id');
        
        if (!$plan_id) {
            $this->session->set_flashdata('error_message', 'Plan ID is required');
            redirect('admin/doctor_subscription_plans');
        }
        
        $this->form_validation->set_rules('name', 'Plan Name', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('duration_days', 'Duration Days', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('perks', 'Perks/Benefits', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_subscription_plans/edit/' . $plan_id);
        }

        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'price' => $this->input->post('price'),
            'duration_days' => $this->input->post('duration_days'),
            'perks' => $this->input->post('perks'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($this->doctor_subscription_plans_model->update_plan($plan_id, $data)) {
            $this->session->set_flashdata('success_message', 'Doctor Subscription Plan Updated Successfully');
            redirect('admin/doctor_subscription_plans');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update doctor subscription plan');
            redirect('admin/doctor_subscription_plans/edit/' . $plan_id);
        }
    }

    function changeStatus($plan_id, $status) {
        if ($this->doctor_subscription_plans_model->change_status($plan_id, $status)) {
            $status_text = ($status == 1) ? 'activated' : 'deactivated';
            $this->session->set_flashdata('success_message', "Doctor Subscription Plan $status_text successfully");
        } else {
            $this->session->set_flashdata('error_message', 'Unable to change status');
        }
        redirect('admin/doctor_subscription_plans');
    }
}
