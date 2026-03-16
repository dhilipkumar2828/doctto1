<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_top_listings extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/doctor_top_listings_model');
        $this->load->model('admin/doctors_model');
        $this->load->model('admin_model');
        $this->data['page_name'] = 'doctor_top_listings';
    }

    function index() {
        $this->data['page_title'] = 'Doctor Top 10 Listings';
        
        // Get filter parameters
        $month_key = $this->input->get('month_key');
        if (!$month_key) {
            $month_key = date('Y-m');
        }
        
        $listings = $this->doctor_top_listings_model->get_listings_by_month($month_key);
        $this->data['listings'] = $listings;
        $this->data['month_key'] = $month_key;
        $this->data['available_doctors'] = $this->doctor_top_listings_model->get_available_doctors($month_key);
        
        $this->admin_view('doctor_top_listings');
    }

    function add() {
        $this->data['page_title'] = 'Add Doctor to Top 10';
        
        $month_key = $this->input->get('month_key');
        if (!$month_key) {
            $month_key = date('Y-m');
        }
        
        $this->data['month_key'] = $month_key;
        $this->data['available_doctors'] = $this->doctor_top_listings_model->get_available_doctors($month_key);
        $this->data['current_count'] = $this->doctor_top_listings_model->get_listings_count($month_key);
        
        $this->admin_view('add_doctor_top_listing');
    }

    function insert() {
        $this->form_validation->set_rules('month_key', 'Month', 'required');
        $this->form_validation->set_rules('doctor_id', 'Doctor', 'required|numeric');
        $this->form_validation->set_rules('position', 'Position', 'numeric|greater_than[0]|less_than_equal_to[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_top_listings/add?month_key=' . $this->input->post('month_key'));
        }

        $month_key = $this->input->post('month_key');
        $doctor_id = $this->input->post('doctor_id');
        $position = $this->input->post('position');

        // Check if doctor already exists in this month
        if ($this->doctor_top_listings_model->is_doctor_listed($month_key, $doctor_id)) {
            $this->session->set_flashdata('error_message', 'Doctor is already in the Top 10 list for this month');
            redirect('admin/doctor_top_listings/add?month_key=' . $month_key);
        }

        // Check if doctor has active subscription
        if (!$this->doctor_top_listings_model->has_active_subscription($doctor_id)) {
            $this->session->set_flashdata('error_message', 'Doctor must have an active subscription to be added to Top 10');
            redirect('admin/doctor_top_listings/add?month_key=' . $month_key);
        }

        // Check if we're at the limit of 10 doctors
        $current_count = $this->doctor_top_listings_model->get_listings_count($month_key);
        if ($current_count >= 10) {
            $this->session->set_flashdata('error_message', 'Maximum 10 doctors allowed per month');
            redirect('admin/doctor_top_listings/add?month_key=' . $month_key);
        }

        // If position is provided, check if it's already taken
        if ($position && $this->doctor_top_listings_model->is_position_taken($month_key, $position)) {
            $this->session->set_flashdata('error_message', 'Position ' . $position . ' is already taken for this month');
            redirect('admin/doctor_top_listings/add?month_key=' . $month_key);
        }

        // If no position provided, auto-assign the next available position
        if (!$position) {
            $position = $current_count + 1;
        }

        $data = array(
            'month_key' => $month_key,
            'doctor_id' => $doctor_id,
            'position' => $position,
            'note' => $this->input->post('note')
        );

        if ($this->doctor_top_listings_model->insert_listing($data)) {
            $this->session->set_flashdata('success_message', 'Doctor added to Top 10 successfully at position ' . $position);
            redirect('admin/doctor_top_listings?month_key=' . $month_key);
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add doctor to Top 10');
            redirect('admin/doctor_top_listings/add?month_key=' . $this->input->post('month_key'));
        }
    }

    function edit($id) {
        if (!$id) {
            redirect('admin/doctor_top_listings');
        }

        $this->data['page_title'] = 'Edit Doctor Top Listing';
        $this->data['listing'] = $this->doctor_top_listings_model->get_listing_by_id($id);
        
        if (!$this->data['listing']) {
            $this->session->set_flashdata('error_message', 'Listing not found');
            redirect('admin/doctor_top_listings');
        }
        
        $this->admin_view('edit_doctor_top_listing');
    }

    function update() {
        $listing_id = $this->input->post('listing_id');
        
        $this->form_validation->set_rules('position', 'Position', 'required|numeric|greater_than[0]|less_than_equal_to[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/doctor_top_listings/edit/' . $listing_id);
        }

        $listing = $this->doctor_top_listings_model->get_listing_by_id($listing_id);
        if (!$listing) {
            $this->session->set_flashdata('error_message', 'Listing not found');
            redirect('admin/doctor_top_listings');
        }

        $position = $this->input->post('position');

        // Check if position is already taken by another doctor
        if ($this->doctor_top_listings_model->is_position_taken_by_other($listing->month_key, $position, $listing_id)) {
            $this->session->set_flashdata('error_message', 'Position ' . $position . ' is already taken for this month');
            redirect('admin/doctor_top_listings/edit/' . $listing_id);
        }

        $data = array(
            'position' => $position,
            'note' => $this->input->post('note')
        );

        if ($this->doctor_top_listings_model->update_listing($listing_id, $data)) {
            $this->session->set_flashdata('success_message', 'Doctor Top Listing Updated Successfully');
            redirect('admin/doctor_top_listings?month_key=' . $listing->month_key);
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update doctor top listing');
            redirect('admin/doctor_top_listings/edit/' . $listing_id);
        }
    }

    function delete($id) {
        if ($this->doctor_top_listings_model->delete_listing($id)) {
            $this->session->set_flashdata('success_message', 'Doctor removed from Top 10 successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to remove doctor from Top 10');
        }
        redirect('admin/doctor_top_listings');
    }

    function update_order() {
        $month_key = $this->input->post('month_key');
        $positions = $this->input->post('positions');
        
        if ($this->doctor_top_listings_model->update_positions($month_key, $positions)) {
            $this->session->set_flashdata('success_message', 'Positions updated successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update positions');
        }
        redirect('admin/doctor_top_listings?month_key=' . $month_key);
    }
}
