<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ads extends CI_Controller {
    public $data;
    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) { 
            redirect('admin/login');
        }
        
        $this->data['page_name'] = 'ads';
        $this->load->model('admin/ads_model');
    }



    function index() {
       
        $data = $this->db->get("ads")->result();
                $this->db->order_by("id","desc");
        $data['ads'] = $data;
        $this->load->view('admin/includes/header', $this->data);
        $doctors = $this->ads_model->get_doctors();
        $this->load->view('admin/ads', $data);
        $this->load->view('admin/includes/footer');
    }


   

    function add() {//
        $this->data['page_title'] = 'Add Doctors Banners';
        $this->data['categories'] = $this->db->get('symptom')->result();
        $this->data['doctors'] = $this->db->get('doctors')->result();
        //print_r($data);exit;

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_ads', $this->data);
         $this->load->view('admin/includes/footer'); 
    }



    function insert(){ 

        $title = $this->input->post('title');
        $appimage = $this->upload_file('appimage');
        $banner_type = $this->input->post('banner_type');
        $category_id = $this->input->post('category_id');
        $doctor_id = $this->input->post('doctor_id');
        $ins = $this->ads_model->insertData($title,$appimage,$banner_type, $category_id,$doctor_id);
        if($ins==true)
        {
            $this->session->set_flashdata('success_message', 'Ads added Successfully');
                redirect('admin/ads');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
                redirect('admin/ads');
        }

    }



    function edit($id=null) 
    {
         $row=$this->ads_model->get_doctorby_id($id);
         $data['banners']=$row;
         $data['title'] = 'Edit Ads';
         $data['categories'] = $this->db->get('symptom')->result();
         $data['doctors'] = $this->db->get('doctors')->result();
  
        $this->load->view('admin/includes/header');
        $this->load->view('admin/edit_ads', $data);
        $this->load->view('admin/includes/footer');
    }



    function update($id=null) {
        $id = $this->input->post('id');
        $row = $this->db->where(array('id'=>$id))->get('ads')->row();

        if($this->upload_file('appimage')!='')
        {
            $appimage=$this->upload_file('appimage');
        }
        else
        {
            $appimage=$row->app_image;
        }

        
        $title = $this->input->post('title');
        $status= $this->input->post('status');
        $banner_type= $this->input->post('banner_type');
        $category_id= $this->input->post('category_id');
        $doctor_id= $this->input->post('doctor_id');
        if($banner_type=='category')
        {
                $data = array(
            'title' => $title,
            'app_image' => $appimage,
            'banner_type'=>$banner_type,
            'category_id'=>$category_id,
            'doctor_id'=>0,
            'status' => $status,
        );
        }
        else
        {
             $data = array(
            'title' => $title,
            'app_image' => $appimage,
            'banner_type'=>$banner_type,
            'category_id'=>0,
            'doctor_id'=>$doctor_id,
            'status' => $status,
        );
        }
        
        $this->db->where('id', $id);
        $update_query = $this->db->update('ads', $data);
       // echo $this->db->last_query(); die;
        if ($update_query) {

            redirect('admin/ads');
            die();
        } else {
            redirect('admin/ads/edit/' . $id);
            die();
        }

    }



 private function upload_file($file_name) {
// echo $file_ext = pathinfo($_FILES[$file_name]["name"], PATHINFO_EXTENSION);
// die;
    if($_FILES[$file_name]['name']!='')
    {

        if($_FILES[$file_name]["size"]<'5114374')
        {
            $upload_path1 = "./uploads/doctor_banners/";
            $config1['upload_path'] = $upload_path1;
            $config1['allowed_types'] = "*";
            // $config1['allowed_types'] = "*";
            $config1['max_size'] = "204800000";
            $img_name1 = strtolower($_FILES[$file_name]['name']);
            $img_name1 = preg_replace('/[^a-zA-Z0-9\.]/', "_", $img_name1);
            $config1['file_name'] = date("YmdHis") . rand(0, 9999999) . "_" . $img_name1;
            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            $this->upload->do_upload($file_name);
            $fileDetailArray1 = $this->upload->data();
            // echo $this->upload->display_errors();
            // die;
            return $fileDetailArray1['file_name'];
        }
        else
        {
            return 'false';
        }
    }
    else
    {
        return '';
    }
    }



function delete($id) {
        if ((isset($id)) && ($id != '')) {
            $parameters = array();
            $parameters['id'] = $id;

            if ($this->ads_model->delete($parameters)) {
               
                $this->session->set_flashdata('success_message', "Deleted Successfully", 'Success');
               redirect('admin/ads');
            } else {
                $this->session->set_flashdata('error_message', 'Please Try Again', 'Error');
                 redirect('admin/ads');
            }
        } else {
            redirect('admin/ads');
        }
    }



}

