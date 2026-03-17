<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Subscription_plans_model $subscription_plans_model
 * @property Doctors_model $doctors_model
 * @property Admin_model $admin_model
 * @property CI_Input $input
 * @property CI_Upload $upload
 * @property CI_Session $session
 */
class Subscription_plans extends MY_Controller {

    public $data;

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }

        $this->load->model('admin/subscription_plans_model');
        $this->load->model('admin/doctors_model');
        $this->load->model('admin_model');
        $this->data['page_name'] = 'subscription_plans';
    }

    function index() {
        $this->data['page_title'] = 'Subscription Plans';
        $plans = $this->subscription_plans_model->get_all_plans();
        $this->data['plans'] = $plans;
        $this->admin_view('subscription_plans');
    }

    function add() {
        $this->data['page_title'] = 'Add Subscription Plan';
        $this->data['consultation_fees'] = $this->db->get('consultation_fees')->result();
        
        // Check if consultation fees exist
        if (empty($this->data['consultation_fees'])) {
            $this->session->set_flashdata('error_message', 'No consultation fees found. Please add consultation fees first.');
            redirect('admin/subscription_plans');
        }
        
        $this->admin_view('add_subscription_plan');
    }

    function insert() {
        $this->form_validation->set_rules('name', 'Plan Name', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('duration_days', 'Duration Days', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('max_doctors_allowed', 'Max Doctors Allowed', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/subscription_plans/add');
        }

        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'price' => $this->input->post('price'),
            'duration_days' => $this->input->post('duration_days'),
            'max_doctors_allowed' => $this->input->post('max_doctors_allowed'),
            'is_active' => 1
        );

        $plan_id = $this->subscription_plans_model->insert_plan($data);

        if ($plan_id) {
            // Insert plan features
            $consultation_fees = $this->input->post('consultation_fees');
            $limit_counts = $this->input->post('limit_counts');
            
            if ($consultation_fees && $limit_counts) {
                foreach ($consultation_fees as $key => $consultation_fee_id) {
                    if (!empty($limit_counts[$key]) && $limit_counts[$key] > 0) {
                        $feature_data = array(
                            'plan_id' => $plan_id,
                            'consultation_fees_id' => $consultation_fee_id,
                            'limit_count' => $limit_counts[$key],
                            'rollover' => isset($_POST['rollover'][$key]) ? 1 : 0
                        );
                        $this->subscription_plans_model->insert_plan_feature($feature_data);
                    }
                }
            }

            $this->session->set_flashdata('success_message', 'Subscription Plan Added Successfully');
            redirect('admin/subscription_plans');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to add subscription plan');
            redirect('admin/subscription_plans/add');
        }
    }

    function edit($id) {
        if (!$id) {
            redirect('admin/subscription_plans');
        }

        $this->data['page_title'] = 'Edit Subscription Plan';
        $this->data['plan'] = $this->subscription_plans_model->get_plan_by_id($id);
        $this->data['plan_features'] = $this->subscription_plans_model->get_plan_features($id);
        $this->data['consultation_fees'] = $this->db->get('consultation_fees')->result();
        $this->data['assigned_doctors'] = $this->subscription_plans_model->get_assigned_doctors($id);
        $this->data['available_doctors'] = $this->subscription_plans_model->get_available_doctors($id);
        
        $this->admin_view('edit_subscription_plan');
    }

    function update() {
        $plan_id = $this->input->post('plan_id');
        
        $this->form_validation->set_rules('name', 'Plan Name', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('duration_days', 'Duration Days', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('max_doctors_allowed', 'Max Doctors Allowed', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('admin/subscription_plans/edit/' . $plan_id);
        }

        // Check if new max_doctors_allowed is less than currently assigned doctors
        $current_assigned_count = $this->subscription_plans_model->get_assigned_doctors_count($plan_id);
        $new_max_doctors = $this->input->post('max_doctors_allowed');
        
        if ($current_assigned_count > $new_max_doctors) {
            $this->session->set_flashdata('error_message', "You can assign only $new_max_doctors doctors to this plan. Currently $current_assigned_count doctors are assigned.");
            redirect('admin/subscription_plans/edit/' . $plan_id);
        }

        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'price' => $this->input->post('price'),
            'duration_days' => $this->input->post('duration_days'),
            'max_doctors_allowed' => $new_max_doctors
        );

        if ($this->subscription_plans_model->update_plan($plan_id, $data)) {
            // Update plan features
            $this->subscription_plans_model->delete_plan_features($plan_id);
            
            $consultation_fees = $this->input->post('consultation_fees');
            $limit_counts = $this->input->post('limit_counts');
            
            if ($consultation_fees && $limit_counts) {
                foreach ($consultation_fees as $key => $consultation_fee_id) {
                    if (!empty($limit_counts[$key]) && $limit_counts[$key] > 0) {
                        $feature_data = array(
                            'plan_id' => $plan_id,
                            'consultation_fees_id' => $consultation_fee_id,
                            'limit_count' => $limit_counts[$key],
                            'rollover' => isset($_POST['rollover'][$key]) ? 1 : 0
                        );
                        $this->subscription_plans_model->insert_plan_feature($feature_data);
                    }
                }
            }

            $this->session->set_flashdata('success_message', 'Subscription Plan Updated Successfully');
            redirect('admin/subscription_plans');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update subscription plan');
            redirect('admin/subscription_plans/edit/' . $plan_id);
        }
    }

    function changeStatus($plan_id, $status) {
        if ($this->subscription_plans_model->change_status($plan_id, $status)) {
            $status_text = ($status == 1) ? 'activated' : 'deactivated';
            $this->session->set_flashdata('success_message', "Subscription Plan $status_text successfully");
        } else {
            $this->session->set_flashdata('error_message', 'Unable to change status');
        }
        session_write_close();
        redirect('admin/subscription_plans');
    }

    function assign_doctor() {
        $plan_id = $this->input->post('plan_id');
        $doctor_id = $this->input->post('doctor_id');
        
        // Check if doctor is already assigned to this plan
        if ($this->subscription_plans_model->is_doctor_assigned($plan_id, $doctor_id)) {
            $this->session->set_flashdata('error_message', 'Doctor is already assigned to this plan');
            redirect('admin/subscription_plans/edit/' . $plan_id);
        }

        // Check if plan has reached max doctors limit
        $current_count = $this->subscription_plans_model->get_assigned_doctors_count($plan_id);
        $max_allowed = $this->subscription_plans_model->get_plan_max_doctors($plan_id);
        
        if ($current_count >= $max_allowed) {
            $this->session->set_flashdata('error_message', "You can assign only $max_allowed doctors to this plan");
            redirect('admin/subscription_plans/edit/' . $plan_id);
        }

        $data = array(
            'plan_id' => $plan_id,
            'doctor_id' => $doctor_id,
            'sort_order' => $current_count + 1
        );

        if ($this->subscription_plans_model->assign_doctor($data)) {
            $this->session->set_flashdata('success_message', 'Doctor assigned successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to assign doctor');
        }
        redirect('admin/subscription_plans/edit/' . $plan_id);
    }

    function remove_doctor($plan_id, $doctor_id) {
        if ($this->subscription_plans_model->remove_doctor($plan_id, $doctor_id)) {
            $this->session->set_flashdata('success_message', 'Doctor removed successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to remove doctor');
        }
        redirect('admin/subscription_plans/edit/' . $plan_id);
    }

    function update_doctor_order() {
        $plan_id = $this->input->post('plan_id');
        $doctor_orders = $this->input->post('doctor_orders');
        
        if ($this->subscription_plans_model->update_doctor_order($plan_id, $doctor_orders)) {
            $this->session->set_flashdata('success_message', 'Doctor order updated successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to update doctor order');
        }
        redirect('admin/subscription_plans/edit/' . $plan_id);
    }

    function manage_doctors($plan_id = NULL) {
        $this->data['page_name'] = 'plan_doctors';
        $this->data['all_plans'] = $this->subscription_plans_model->get_active_plans();
        
        if ($plan_id) {
            $this->data['current_plan_id'] = $plan_id;
            $this->data['plan'] = $this->subscription_plans_model->get_plan_by_id($plan_id);
            if (!$this->data['plan']) {
                $this->session->set_flashdata('error_message', 'Plan not found');
                redirect('admin/subscription_plans/manage_doctors');
            }
            $this->data['page_title'] = 'Manage Doctors - ' . $this->data['plan']->name;
            $this->data['assigned_doctors'] = $this->subscription_plans_model->get_assigned_doctors($plan_id);
            $this->data['available_doctors'] = $this->subscription_plans_model->get_available_doctors($plan_id);
        } else {
            $this->data['current_plan_id'] = NULL;
            $this->data['plan'] = NULL;
            $this->data['page_title'] = 'Manage Doctors - All Plans';
            $this->data['assigned_doctors'] = $this->subscription_plans_model->get_all_assigned_doctors();
            $this->data['available_doctors'] = $this->subscription_plans_model->get_all_available_doctors_for_assignments();
        }
        
        $this->admin_view('manage_plan_doctors');
    }

    function add_doctors() {
        $this->data['page_name'] = 'plan_doctors';
        $this->data['page_title'] = 'Add Doctors to Plan';
        $this->data['all_plans'] = $this->subscription_plans_model->get_active_plans();
        $this->data['available_doctors'] = $this->subscription_plans_model->get_all_available_doctors_for_assignments();
        $this->admin_view('add_plan_doctors');
    }

    function assign_doctor_from_manage() {
        $plan_id = $this->input->post('plan_id');
        $doctor_ids = $this->input->post('doctor_ids');
        $title = $this->input->post('title');
        
        if (empty($plan_id) || empty($doctor_ids)) {
            $this->session->set_flashdata('error_message', 'Please select both plan and doctor(s)');
            redirect('admin/subscription_plans/add_doctors');
        }

        if (!is_array($doctor_ids)) {
            $doctor_ids = array($doctor_ids);
        }

        $appimage = $this->upload_file('appimage');

        $success_count = 0;
        $error_count = 0;
        $max_allowed = $this->subscription_plans_model->get_plan_max_doctors($plan_id);

        foreach ($doctor_ids as $doctor_id) {
            $current_count = $this->subscription_plans_model->get_assigned_doctors_count($plan_id);
            
            if ($current_count >= $max_allowed) {
                $error_count++;
                continue;
            }

            // Check if doctor is already assigned to ANY plan
            if ($this->subscription_plans_model->is_doctor_assigned_to_any_plan($doctor_id)) {
                $error_count++;
                continue;
            }

            $data = array(
                'plan_id' => $plan_id,
                'doctor_id' => $doctor_id,
                'title' => $title,
                'app_image' => $appimage,
                'sort_order' => $current_count + 1
            );

            if ($this->subscription_plans_model->assign_doctor($data)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        if ($success_count > 0) {
            $this->session->set_flashdata('success_message', "$success_count doctor(s) assigned successfully");
        }
        if ($error_count > 0) {
            $this->session->set_flashdata('error_message', "Failed to assign $error_count doctor(s). Check limits or existing assignments.");
        }

        redirect('admin/subscription_plans/manage_doctors');
    }

    private function upload_file($file_name) {
        if($_FILES[$file_name]['name']!='') {
            if($_FILES[$file_name]["size"]<'5114374') {
                $upload_path1 = "./uploads/doctor_banners/";
                if (!is_dir($upload_path1)) {
                    mkdir($upload_path1, 0777, TRUE);
                }
                $config1['upload_path'] = $upload_path1;
                $config1['allowed_types'] = "*";
                $config1['max_size'] = "204800000";
                $img_name1 = strtolower($_FILES[$file_name]['name']);
                $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
                $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
                $this->load->library('upload', $config1);
                $this->upload->initialize($config1);
                if($this->upload->do_upload($file_name)) {
                    $fileDetailArray1 = $this->upload->data();
                    return $fileDetailArray1['file_name'];
                }
            }
        }
        return '';
    }

    function remove_doctor_from_manage($plan_id, $doctor_id) {
        if ($this->subscription_plans_model->remove_doctor($plan_id, $doctor_id)) {
            $this->session->set_flashdata('success_message', 'Doctor removed successfully');
        } else {
            $this->session->set_flashdata('error_message', 'Unable to remove doctor');
        }
        redirect('admin/subscription_plans/manage_doctors/' . $plan_id);
    }
}
