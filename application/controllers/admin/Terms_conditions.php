<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Terms_conditions extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/terms_conditions_model');
        $this->data['page_name'] = 'terms_conditions';
    }

    function index() {
        $this->data['page_title'] = 'Terms & Conditions Management';
        
        // Get filter parameters
        $plan_type = $this->input->get('plan_type');
        $status = $this->input->get('status');
        
        $terms = $this->terms_conditions_model->get_all_terms($plan_type, $status);
        $this->data['terms'] = $terms;
        $this->data['filter_plan_type'] = $plan_type;
        $this->data['filter_status'] = $status;
        
        $this->admin_view('terms_conditions');
    }

    function add() {
        $this->data['page_title'] = 'Add Terms & Conditions';
        $this->data['plan_types'] = ['user' => 'User Subscriptions', 'doctor' => 'Doctor Subscriptions', 'both' => 'Both'];
        
        $this->admin_view('add_terms_conditions');
    }

    function insert() {
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('content', 'Content', 'required|trim');
        $this->form_validation->set_rules('plan_type', 'Plan Type', 'required|in_list[user,doctor,both]');
        $this->form_validation->set_rules('version', 'Version', 'required|trim');
        $this->form_validation->set_rules('effective_date', 'Effective Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/terms_conditions/add');
        }

        $data = array(
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'plan_type' => $this->input->post('plan_type'),
            'subscription_plan_id' => $this->input->post('subscription_plan_id') ?: null,
            'version' => $this->input->post('version'),
            'effective_date' => $this->input->post('effective_date'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'created_by' => $this->session->userdata('admin_login')['admin_id']
        );

        if ($this->terms_conditions_model->insert_terms($data)) {
            $this->session->set_flashdata('success_message', 'Terms & Conditions Added Successfully');
            redirect('admin/terms_conditions');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add terms & conditions');
            redirect('admin/terms_conditions/add');
        }
    }

    function edit($id) {
        if (!$id) {
            redirect('admin/terms_conditions');
        }

        $this->data['page_title'] = 'Edit Terms & Conditions';
        $this->data['terms'] = $this->terms_conditions_model->get_terms_by_id($id);
        $this->data['plan_types'] = ['user' => 'User Subscriptions', 'doctor' => 'Doctor Subscriptions', 'both' => 'Both'];
        
        if (!$this->data['terms']) {
            $this->session->set_flashdata('error_message', 'Terms & Conditions not found');
            redirect('admin/terms_conditions');
        }
        
        $this->admin_view('edit_terms_conditions');
    }

    function update($id) {
        if (!$id) {
            redirect('admin/terms_conditions');
        }

        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('content', 'Content', 'required|trim');
        $this->form_validation->set_rules('plan_type', 'Plan Type', 'required|in_list[user,doctor,both]');
        $this->form_validation->set_rules('version', 'Version', 'required|trim');
        $this->form_validation->set_rules('effective_date', 'Effective Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/terms_conditions/edit/' . $id);
        }

        $data = array(
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'plan_type' => $this->input->post('plan_type'),
            'subscription_plan_id' => $this->input->post('subscription_plan_id') ?: null,
            'version' => $this->input->post('version'),
            'effective_date' => $this->input->post('effective_date'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );

        if ($this->terms_conditions_model->update_terms($id, $data)) {
            $this->session->set_flashdata('success_message', 'Terms & Conditions Updated Successfully');
            redirect('admin/terms_conditions');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update terms & conditions');
            redirect('admin/terms_conditions/edit/' . $id);
        }
    }

    function changeStatus($id, $status) {
        if (!$id) {
            redirect('admin/terms_conditions');
        }

        $valid_statuses = ['active', 'inactive'];
        if (!in_array($status, $valid_statuses)) {
            $this->session->set_flashdata('error_message', 'Invalid status');
            redirect('admin/terms_conditions');
        }

        $status_value = ($status == 'active') ? 1 : 0;

        if ($this->terms_conditions_model->change_status($id, $status_value)) {
            $this->session->set_flashdata('success_message', 'Status changed successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to change status');
        }

        redirect('admin/terms_conditions');
    }

    function delete($id) {
        if (!$id) {
            redirect('admin/terms_conditions');
        }

        if ($this->terms_conditions_model->delete_terms($id)) {
            $this->session->set_flashdata('success_message', 'Terms & Conditions deleted successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to delete terms & conditions');
        }

        redirect('admin/terms_conditions');
    }

    function view($id) {
        if (!$id) {
            redirect('admin/terms_conditions');
        }

        $this->data['page_title'] = 'View Terms & Conditions';
        $this->data['terms'] = $this->terms_conditions_model->get_terms_by_id($id);
        
        if (!$this->data['terms']) {
            $this->session->set_flashdata('error_message', 'Terms & Conditions not found');
            redirect('admin/terms_conditions');
        }
        
        // Get sections count and sections
        $this->data['sections'] = $this->terms_conditions_model->get_terms_sections($id);
        $this->data['sections_count'] = count($this->data['sections']);
        
        // Get acceptance count (placeholder for now)
        $this->data['acceptance_count'] = 0; // TODO: Implement acceptance counting
        
        $this->admin_view('view_terms_conditions');
    }

    function sections($terms_id) {
        if (!$terms_id) {
            redirect('admin/terms_conditions');
        }

        $this->data['page_title'] = 'Manage Terms Sections';
        $this->data['terms'] = $this->terms_conditions_model->get_terms_by_id($terms_id);
        $this->data['sections'] = $this->terms_conditions_model->get_terms_sections($terms_id);
        
        if (!$this->data['terms']) {
            $this->session->set_flashdata('error_message', 'Terms & Conditions not found');
            redirect('admin/terms_conditions');
        }
        
        $this->admin_view('terms_sections');
    }

    function addSection() {
        $this->form_validation->set_rules('terms_id', 'Terms ID', 'required|numeric');
        $this->form_validation->set_rules('section_title', 'Section Title', 'required|trim');
        $this->form_validation->set_rules('section_content', 'Section Content', 'required|trim');
        $this->form_validation->set_rules('section_order', 'Section Order', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/terms_conditions/sections/' . $this->input->post('terms_id'));
        }

        $data = array(
            'terms_id' => $this->input->post('terms_id'),
            'section_title' => $this->input->post('section_title'),
            'section_content' => $this->input->post('section_content'),
            'section_order' => $this->input->post('section_order') ?: 0,
            'is_required' => $this->input->post('is_required') ? 1 : 0
        );

        if ($this->terms_conditions_model->insert_section($data)) {
            $this->session->set_flashdata('success_message', 'Section added successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add section');
        }

        redirect('admin/terms_conditions/sections/' . $this->input->post('terms_id'));
    }

    function updateSection() {
        $this->form_validation->set_rules('section_id', 'Section ID', 'required|numeric');
        $this->form_validation->set_rules('section_title', 'Section Title', 'required|trim');
        $this->form_validation->set_rules('section_content', 'Section Content', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/terms_conditions');
        }

        $section_id = $this->input->post('section_id');
        $data = array(
            'section_title' => $this->input->post('section_title'),
            'section_content' => $this->input->post('section_content'),
            'section_order' => $this->input->post('section_order') ?: 0,
            'is_required' => $this->input->post('is_required') ? 1 : 0
        );

        if ($this->terms_conditions_model->update_section($section_id, $data)) {
            $this->session->set_flashdata('success_message', 'Section updated successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update section');
        }

        redirect('admin/terms_conditions/sections/' . $this->input->post('terms_id'));
    }

    function deleteSection($section_id, $terms_id) {
        if (!$section_id || !$terms_id) {
            redirect('admin/terms_conditions');
        }

        if ($this->terms_conditions_model->delete_section($section_id)) {
            $this->session->set_flashdata('success_message', 'Section deleted successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to delete section');
        }

        redirect('admin/terms_conditions/sections/' . $terms_id);
    }
}
