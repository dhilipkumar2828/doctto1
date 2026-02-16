<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_subscriptions extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/doctor_subscriptions_model');
        $this->load->model('admin/doctors_model');
        $this->load->model('admin_model');
        $this->data['page_name'] = 'doctor_subscriptions';
    }

    function index() {
        $this->data['page_title'] = 'Doctor Subscriptions';
        
        // Get filter parameters
        $doctor_id = $this->input->get('doctor_id');
        $status = $this->input->get('status');
        
        $subscriptions = $this->doctor_subscriptions_model->get_all_subscriptions($doctor_id, $status);
        $this->data['subscriptions'] = $subscriptions;
        $this->data['doctors'] = $this->doctors_model->get_doctors();
        $this->data['filter_doctor_id'] = $doctor_id;
        $this->data['filter_status'] = $status;
        
        $this->admin_view('doctor_subscriptions');
    }

    function add() {
        $this->data['page_title'] = 'Add Doctor Subscription';
        $this->data['doctors'] = $this->doctors_model->get_doctors();
        $this->data['plans'] = $this->doctor_subscriptions_model->get_active_plans();
        $this->admin_view('add_doctor_subscription');
    }

    function insert() {
        $this->form_validation->set_rules('doctor_id', 'Doctor', 'required|numeric');
        $this->form_validation->set_rules('doctor_subscription_plan_id', 'Plan', 'required|numeric');
        $this->form_validation->set_rules('start_at', 'Start Date', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,expired,cancelled,pending]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_subscriptions/add');
        }

        $start_at = $this->input->post('start_at');
        $plan_id = $this->input->post('doctor_subscription_plan_id');
        
        // Get plan duration
        $plan = $this->doctor_subscriptions_model->get_plan_by_id($plan_id);
        if (!$plan) {
            $this->session->set_flashdata('error_message', 'Invalid plan selected');
            redirect('admin/doctor_subscriptions/add');
        }

        $end_at = date('Y-m-d H:i:s', strtotime($start_at . ' + ' . $plan->duration_days . ' days'));

        $data = array(
            'doctor_id' => $this->input->post('doctor_id'),
            'doctor_subscription_plan_id' => $plan_id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'status' => $this->input->post('status'),
            'auto_renew' => $this->input->post('auto_renew') ? 1 : 0,
            'phonepe_agreement_id' => $this->input->post('phonepe_agreement_id')
        );

        if ($this->doctor_subscriptions_model->insert_subscription($data)) {
            $this->session->set_flashdata('success_message', 'Doctor Subscription Added Successfully');
            redirect('admin/doctor_subscriptions');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add doctor subscription');
            redirect('admin/doctor_subscriptions/add');
        }
    }

    function edit($id) {
        if (!$id) {
            redirect('admin/doctor_subscriptions');
        }

        $this->data['page_title'] = 'Edit Doctor Subscription';
        $this->data['subscription'] = $this->doctor_subscriptions_model->get_subscription_by_id($id);
        
        if (!$this->data['subscription']) {
            $this->session->set_flashdata('error_message', 'Subscription not found');
            redirect('admin/doctor_subscriptions');
        }
        
        $this->data['doctors'] = $this->doctors_model->get_doctors();
        $this->data['plans'] = $this->doctor_subscriptions_model->get_active_plans();
        
        $this->admin_view('edit_doctor_subscription');
    }

    function update() {
        $subscription_id = $this->input->post('subscription_id');
        
        $this->form_validation->set_rules('doctor_id', 'Doctor', 'required|numeric');
        $this->form_validation->set_rules('doctor_subscription_plan_id', 'Plan', 'required|numeric');
        $this->form_validation->set_rules('start_at', 'Start Date', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,expired,cancelled,pending]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_subscriptions/edit/' . $subscription_id);
        }

        $start_at = $this->input->post('start_at');
        $plan_id = $this->input->post('doctor_subscription_plan_id');
        
        // Get plan duration
        $plan = $this->doctor_subscriptions_model->get_plan_by_id($plan_id);
        if (!$plan) {
            $this->session->set_flashdata('error_message', 'Invalid plan selected');
            redirect('admin/doctor_subscriptions/edit/' . $subscription_id);
        }

        $end_at = date('Y-m-d H:i:s', strtotime($start_at . ' + ' . $plan->duration_days . ' days'));

        $data = array(
            'doctor_id' => $this->input->post('doctor_id'),
            'doctor_subscription_plan_id' => $plan_id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'status' => $this->input->post('status'),
            'auto_renew' => $this->input->post('auto_renew') ? 1 : 0,
            'phonepe_agreement_id' => $this->input->post('phonepe_agreement_id')
        );

        if ($this->doctor_subscriptions_model->update_subscription($subscription_id, $data)) {
            $this->session->set_flashdata('success_message', 'Doctor Subscription Updated Successfully');
            redirect('admin/doctor_subscriptions');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update doctor subscription');
            redirect('admin/doctor_subscriptions/edit/' . $subscription_id);
        }
    }

    function changeStatus($subscription_id, $status) {
        if ($this->doctor_subscriptions_model->change_status($subscription_id, $status)) {
            $status_text = $status;
            $this->session->set_flashdata('success_message', "Doctor Subscription status changed to $status_text successfully");
        } else {
            $this->session->set_flashdata('error_message', 'Unable to change status');
        }
        redirect('admin/doctor_subscriptions');
    }
}
