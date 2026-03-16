<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Doctor_categories extends CI_Controller {
    public $data;
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('admin_login')['logged_in'] != true) {
            //$this->session->set_flashdata('error', 'Session Timed Out');
            redirect('admin/login');
        }
    }

    function index() {
        $this->data['page_name'] = 'doctor_categories';
        $this->data['title'] = 'Doctor categories';
        $this->data['categories'] = $this->db->get('doctor_categories')->result();
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/doctor-categories', $this->data);
        $this->load->view('admin/includes/footer');
    }



    function add() 
    {
        $this->data['title'] = 'Add Doctor Category';
        $this->load->view('admin/includes/header', $this->data);
        $this->load->view('admin/add_doctor_category', $this->data);
        $this->load->view('admin/includes/footer');
    }

function make_seo_name($title) {
        return preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($title))));
    }

    function insert() {

        $category_name = $this->input->get_post('category_name');

        $cat_qry=$this->db->query("select * from doctor_categories where category_name='".$category_name."'");
        if($cat_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already exist ( Category Name )');
            redirect('admin/doctor_categories/add');
            die();
        }
        $status = $this->input->get_post('status');
        $priority = $this->input->get_post('priority');

            if(!empty($_FILES["app_image"]["name"]))
            {
                $img_k_image = "Doc_category_".$i."_".date('YmdHis').".jpg";
                if (file_exists("./uploads/doctor_categories/" . $img_k_image))
                {
                     $k_image= $img_k_image;
                }
                else
                {
                  move_uploaded_file($_FILES["app_image"]["tmp_name"], "./uploads/doctor_categories/" . $img_k_image);
                 $k_image=$img_k_image;
                }
            }
            else
            {
                $k_image="";
            }

            $seo_url = $this->make_seo_name($category_name);
        $data = array(

            'category_name' => $category_name,
            'app_image' => $k_image,
            'status' => $status,
            'created_at' => time(),
            'seo_url'=>$seo_url,
            'priority'=>$priority
        );

        $insert_query = $this->db->insert('doctor_categories', $data);

        if ($insert_query) {
            redirect('admin/doctor_categories');
            die();
        } else {
            redirect('admin/doctor_categories/add');
            die();
        }
    }



    function edit_category($cat_id) {

        $this->data['title'] = 'Edit Doctor Category';

        $this->data['doctor_category'] = $this->db->get_where('doctor_categories', ['id' => $cat_id])->row();

        $this->load->view('admin/includes/header');

        $this->load->view('admin/edit_doctor_category', $this->data);

        $this->load->view('admin/includes/footer');

    }



    function update() 
    {
        $cat_id = $this->input->get_post('cat_id');
        $category_name = $this->input->get_post('category_name');
        $status = $this->input->get_post('status');
        $priority = $this->input->get_post('priority');
        $seo_url = $this->make_seo_name($category_name);


         $cat_qry=$this->db->query("select * from doctor_categories where id!='".$cat_id."' and category_name='".$category_name."'");
        if($cat_qry->num_rows()>0)
        {
            $this->session->set_flashdata('error_message', 'Already exist ( Category Name )');
            redirect('admin/doctor_categories/edit_category/' . $cat_id);
            die();
        }

        $data = array(
            'category_name' => $category_name,
            'seo_url'=>$seo_url,
            'status' => $status,
            'updated_at' => time(),
             'priority'=>$priority
        );
            if(!empty($_FILES["app_image"]["name"]))
            {
                $img_k_image = "Doc_category_".date('YmdHis').".jpg";
                if (file_exists("./uploads/doctor_categories/" . $img_k_image))
                {
                     $k_image= $img_k_image;
                }
                else
                {
                  move_uploaded_file($_FILES["app_image"]["tmp_name"], "./uploads/doctor_categories/" . $img_k_image);
                  $k_image=$img_k_image;
                }
                $data['app_image'] = $k_image;
                $this->db->where('id', $cat_id);
                $update_query = $this->db->update('doctor_categories', $data);
            }
            else
            {
                $this->db->where('id', $cat_id);
                $update_query = $this->db->update('doctor_categories', $data);
            }
        if ($update_query) {
            redirect('admin/doctor_categories');
            die();
        } else {
            redirect('admin/doctor_categories/edit_category/' . $cat_id);
            die();
        }

    }



    function delete($cat_id) {


        $this->db->where('cat_id', $cat_id);
        $subcatFound = $this->db->get('sub_categories')->result();
        if (count($subcatFound) > 0) 
        {
            $this->session->set_flashdata('error_message', 'Some subcategories are assigned, Unable to delete');
            redirect('admin/doctor_categories');

        } 
        else 
        {
            $this->db->where('id', $cat_id);

            if ($this->db->delete('doctor_categories')) {

                $this->session->set_flashdata('success_message', 'Category Deleted Successfully');

                redirect('admin/doctor_categories');

            } else {

                $this->session->set_flashdata('error_message', 'Unable to delete');

                redirect('admin/doctor_categories');

            }

        }

    }



}

