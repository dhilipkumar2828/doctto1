<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctor_banners extends CI_Controller {



    public $data;



    function __construct() {

        parent::__construct();

        if ($this->session->userdata('admin_login')['logged_in'] != true) { 

            //$this->session->set_flashdata('error', 'Session Timed Out');

            redirect('admin/login');

        }
         $this->data['page_name'] = 'doctor_banners';
         $this->load->model('admin/doctors_banners_model');
    }



    function index() {
       
        $qry = $this->db->query("select * from doctor_banners order by id desc");
        $data['banners'] = $qry->result();
        $this->load->view('admin/includes/header', $this->data);
        $doctors = $this->doctors_banners_model->get_doctors();
        $this->load->view('admin/doctor_banners', $data);
        $this->load->view('admin/includes/footer');
    }


   

    function add() {//
        $this->data['page_title'] = 'Add Doctors Banners';
        $this->data['categories'] = $this->db->get('symptom')->result();
        $this->data['doctors'] = $this->db->get('doctors')->result();
        //print_r($data);exit;

        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_doctor_banner', $this->data);
         $this->load->view('admin/includes/footer'); 
    }



    function insert(){ 

        $title = $this->input->post('title');
        $appimage = $this->upload_file('appimage');
        $banner_type = $this->input->post('banner_type');
        $category_id = $this->input->post('category_id');
        $doctor_id = $this->input->post('doctor_id');
        $ins = $this->doctors_banners_model->insertData($title,$appimage,$banner_type, $category_id,$doctor_id);
       
        

         if($ins==true)
        {
            $this->session->set_flashdata('success_message', 'Doctors added Successfully');
                redirect('admin/doctor_banners');
        }
        else
        {
            $this->session->set_flashdata('error_message', 'Something went wrong, please try again');
                redirect('admin/doctor_banners');
        }

    }



    function edit($id=null) {
        
       $row=$this->doctors_banners_model->get_doctorby_id($id);
        $data['banners']=$row;
        $data['title'] = 'Edit Banner';
         $data['categories'] = $this->db->get('symptom')->result();
         $data['doctors'] = $this->db->get('doctors')->result();
        //$this->admin_view('edit_doctor_banners');


        // $data['banners'] = $this->db->get_where('doctor_banners', ['id' => $id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_doctor_banners', $data);

        $this->load->view('admin/includes/footer');

    }



    function update($id=null) {
        $id = $this->input->post('id');
        $row = $this->db->where(array('id'=>$id))->get('doctor_banners')->row();

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
        $update_query = $this->db->update('doctor_banners', $data);
       // echo $this->db->last_query(); die;
        if ($update_query) {

            redirect('admin/doctor_banners');
            die();
        } else {
            redirect('admin/doctor_banners/edit/' . $id);
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

            if ($this->doctors_banners_model->delete($parameters)) {
               
                $this->session->set_flashdata('success_message', "'Deleted Successfully', 'Success'");
               redirect('admin/doctor_banners');
            } else {
                $this->session->set_flashdata('error_message', "'Please Try Again', 'Error'");
                 redirect('admin/doctor_banners');
            }
        } else {
            redirect('admin/doctor_banners');
        }
    }



}

